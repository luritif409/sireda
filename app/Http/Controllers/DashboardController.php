<?php

namespace App\Http\Controllers;

use App\Models\Revision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function mahasiswa(Request $request): View
    {
        $mahasiswa = $request->user();

        // Get all revisions for this mahasiswa, grouped by dosen (optimized query)
        $revisions = Revision::with(['dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('created_at')
            ->get();

        // Group revisions by dosen for recap (optimized - single pass)
        $rekapByDosen = $revisions->groupBy('dosen_id')->map(function ($group) {
            return [
                'dosen' => $group->first()->dosen,
                'total' => $group->count(),
                'belum_diperbaiki' => $group->where('status', 'belum_diperbaiki')->count(),
                'sudah_diperbaiki' => $group->where('status', 'sudah_diperbaiki')->count(),
                'revisions' => $group,
            ];
        });

        return view('dashboard.mahasiswa', compact('revisions', 'rekapByDosen'));
    }

    public function exportRekapPDF(Request $request): Response
    {
        $mahasiswa = $request->user();
        
        // Get all revisions for this mahasiswa
        $revisions = Revision::with(['dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('created_at')
            ->get();

        // Group revisions by dosen for recap
        $rekapByDosen = $revisions->groupBy('dosen_id')->map(function ($group) {
            return [
                'dosen' => $group->first()->dosen,
                'total' => $group->count(),
                'belum_diperbaiki' => $group->where('status', 'belum_diperbaiki')->count(),
                'sudah_diperbaiki' => $group->where('status', 'sudah_diperbaiki')->count(),
                'revisions' => $group,
            ];
        });

        Log::info('dashboard.export_rekap_pdf', [
            'mahasiswa_id' => $mahasiswa->id,
            'total_revisions' => $revisions->count(),
            'total_dosen' => $rekapByDosen->count()
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.rekap-pdf', [
            'mahasiswa' => $mahasiswa,
            'revisions' => $revisions,
            'rekapByDosen' => $rekapByDosen,
        ])->setPaper('a4', 'portrait');

        $filename = 'rekap-revisi-' . $mahasiswa->name . '-' . date('Y-m-d') . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);

        return $pdf->download($filename);
    }

    public function exportAllRevisionsPDF(Request $request): Response
    {
        $mahasiswa = $request->user();
        
        // Get all revisions for this mahasiswa
        $revisions = Revision::with(['dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('dosen_id')
            ->orderByDesc('created_at')
            ->get();

        Log::info('dashboard.export_all_revisions_pdf', [
            'mahasiswa_id' => $mahasiswa->id,
            'total_revisions' => $revisions->count()
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.all-revisions-pdf', [
            'mahasiswa' => $mahasiswa,
            'revisions' => $revisions,
        ])->setPaper('a4', 'portrait');

        $filename = 'semua-revisi-' . $mahasiswa->name . '-' . date('Y-m-d') . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);

        return $pdf->download($filename);
    }

    public function dosen(Request $request): View
    {
        // Optimized: limit revisions untuk performa lebih baik
        $revisions = Revision::with(['mahasiswa'])
            ->where('dosen_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->limit(50) // Limit untuk performa
            ->get()
            ->groupBy('mahasiswa_id');

        return view('dashboard.dosen', compact('revisions'));
    }

    public function admin(Request $request): View
    {
        // Pisahkan data berdasarkan role
        $dosenList = \App\Models\User::where('role', 'dosen')
            ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
            ->orderBy('name')
            ->get();

        $mahasiswaList = \App\Models\User::where('role', 'mahasiswa')
            ->with(['dosenPembimbing'])
            ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
            ->orderBy('name')
            ->get();

        // Pre-load semua revisi untuk menghitung status "selesai semua revisi"
        $mahasiswaIds = $mahasiswaList->pluck('id');
        $allRevisions = \App\Models\Revision::whereIn('mahasiswa_id', $mahasiswaIds)
            ->get()
            ->groupBy('mahasiswa_id');

        // Hitung status "selesai semua revisi" untuk setiap mahasiswa
        $mahasiswaList->each(function ($mahasiswa) use ($allRevisions) {
            $revisions = $allRevisions->get($mahasiswa->id, collect());
            $totalRevisi = $revisions->count();
            $revisiBelumDiperbaiki = $revisions->where('status', 'belum_diperbaiki')->count();
            
            // Selesai semua revisi jika ada revisi dan tidak ada yang belum diperbaiki
            $mahasiswa->selesai_semua_revisi = $totalRevisi > 0 && $revisiBelumDiperbaiki === 0;
        });

        $adminList = \App\Models\User::where('role', 'admin')
            ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
            ->orderBy('name')
            ->get();

        return view('dashboard.admin', compact('dosenList', 'mahasiswaList', 'adminList'));
    }
}



