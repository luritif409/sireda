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
        
        Log::info('dashboard.mahasiswa.start', [
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_name' => $mahasiswa->name,
            'mahasiswa_email' => $mahasiswa->email
        ]);

        // Get all revisions for this mahasiswa, grouped by dosen
        $revisions = Revision::with(['dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('created_at')
            ->get();

        Log::info('dashboard.mahasiswa.revisions_fetched', [
            'mahasiswa_id' => $mahasiswa->id,
            'total_revisions' => $revisions->count(),
            'revision_ids' => $revisions->pluck('id')->toArray()
        ]);

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

        Log::info('dashboard.mahasiswa.rekap_calculated', [
            'mahasiswa_id' => $mahasiswa->id,
            'total_dosen' => $rekapByDosen->count(),
            'rekap_summary' => $rekapByDosen->map(function($r) {
                return [
                    'dosen_id' => $r['dosen']->id,
                    'dosen_name' => $r['dosen']->name,
                    'total' => $r['total'],
                    'belum_diperbaiki' => $r['belum_diperbaiki'],
                    'sudah_diperbaiki' => $r['sudah_diperbaiki']
                ];
            })->values()->toArray()
        ]);

        Log::info('dashboard.mahasiswa.complete', [
            'mahasiswa_id' => $mahasiswa->id
        ]);

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
        $revisions = Revision::with(['mahasiswa'])
            ->where('dosen_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('mahasiswa_id');

        return view('dashboard.dosen', compact('revisions'));
    }

    public function admin(Request $request): View
    {
        $users = \App\Models\User::whereIn('role', ['dosen', 'mahasiswa'])
            ->with(['dosenPembimbing'])
            ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
            ->get();

        return view('dashboard.admin', compact('users'));
    }
}



