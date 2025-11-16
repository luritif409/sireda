<?php

namespace App\Http\Controllers;

use App\Models\Revision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RevisiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Revision::with(['mahasiswa', 'dosen'])->orderByDesc('created_at');
        if ($request->filled('tahap')) {
            $query->where('tahap', $request->string('tahap'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        // Dosen sees their created revisions
        $query->where('dosen_id', $request->user()->id);
        $revisions = $query->paginate(10);

        return view('revisions.index', compact('revisions'));
    }

    public function create(Request $request): View
    {
        // Filter mahasiswa yang BUKAN bimbingan dosen yang login (selain dosen pembimbing)
        $mahasiswaList = User::where('role', 'mahasiswa')
            ->where(function($query) use ($request) {
                $query->where('dosen_pembimbing_id', '!=', $request->user()->id)
                      ->orWhereNull('dosen_pembimbing_id');
            })
            ->orderBy('name')
            ->get();
        return view('revisions.create', compact('mahasiswaList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:users,id'],
            'tahap' => ['required', 'in:proposal,sidang_akhir'],
            'tanggal_revisi' => ['required', 'date'],
            'isi_revisi' => ['required', 'string'],
            'status' => ['required', 'in:belum_diperbaiki,sudah_diperbaiki'],
            'bukti_file' => ['nullable', 'file', 'mimetypes:application/pdf', 'max:5120'],
        ]);

        // Verify that the selected user is a mahasiswa (not a dosen)
        $mahasiswa = User::find((int) $validated['mahasiswa_id']);
        if (!$mahasiswa || $mahasiswa->role !== 'mahasiswa') {
            return redirect()->back()
                ->withErrors(['mahasiswa_id' => 'Mahasiswa yang dipilih tidak valid.'])
                ->withInput();
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('bukti_revisi', 'public');
        }

        $revision = Revision::create([
            'mahasiswa_id' => (int) $validated['mahasiswa_id'],
            'dosen_id' => $request->user()->id,
            'tahap' => $validated['tahap'],
            'tanggal_revisi' => $validated['tanggal_revisi'],
            'isi_revisi' => $validated['isi_revisi'],
            'status' => $validated['status'],
            'token' => (string) Str::uuid(),
            'bukti_file_path' => $buktiPath,
        ]);

        Log::info('revisions.store', ['user_id' => $request->user()->id, 'revision_id' => $revision->id]);

        // simple database notification for mahasiswa (optional)
        try {
            $mahasiswa = User::find((int) $validated['mahasiswa_id']);
            if ($mahasiswa) {
                $mahasiswa->notify(new \App\Notifications\RevisionCreatedNotification($revision));
            }
        } catch (\Throwable $e) {
            Log::warning('revisions.notify_failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('revisions.show', $revision)->with('status', 'Revisi dibuat');
    }

    public function show(Request $request, Revision $revision): View
    {
        $this->authorizeDosen($request->user()->id, $revision->dosen_id);
        return view('revisions.show', compact('revision'));
    }

    public function edit(Request $request, Revision $revision): View
    {
        $this->authorizeDosen($request->user()->id, $revision->dosen_id);
        // Filter mahasiswa yang BUKAN bimbingan dosen yang login (selain dosen pembimbing)
        $mahasiswaList = User::where('role', 'mahasiswa')
            ->where(function($query) use ($request) {
                $query->where('dosen_pembimbing_id', '!=', $request->user()->id)
                      ->orWhereNull('dosen_pembimbing_id');
            })
            ->orderBy('name')
            ->get();
        return view('revisions.edit', compact('revision', 'mahasiswaList'));
    }

    public function update(Request $request, Revision $revision): RedirectResponse
    {
        $this->authorizeDosen($request->user()->id, $revision->dosen_id);

        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:users,id'],
            'tahap' => ['required', 'in:proposal,sidang_akhir'],
            'tanggal_revisi' => ['required', 'date'],
            'isi_revisi' => ['required', 'string'],
            'status' => ['required', 'in:belum_diperbaiki,sudah_diperbaiki'],
            'bukti_file' => ['nullable', 'file', 'mimetypes:application/pdf', 'max:5120'],
        ]);

        // Verify that the selected user is a mahasiswa (not a dosen)
        $mahasiswa = User::find((int) $validated['mahasiswa_id']);
        if (!$mahasiswa || $mahasiswa->role !== 'mahasiswa') {
            return redirect()->back()
                ->withErrors(['mahasiswa_id' => 'Mahasiswa yang dipilih tidak valid.'])
                ->withInput();
        }

        if ($request->hasFile('bukti_file')) {
            $revision->bukti_file_path = $request->file('bukti_file')->store('bukti_revisi', 'public');
        }

        $revision->fill([
            'mahasiswa_id' => (int) $validated['mahasiswa_id'],
            'tahap' => $validated['tahap'],
            'tanggal_revisi' => $validated['tanggal_revisi'],
            'isi_revisi' => $validated['isi_revisi'],
            'status' => $validated['status'],
        ])->save();

        Log::info('revisions.update', ['user_id' => $request->user()->id, 'revision_id' => $revision->id]);

        return redirect()->route('revisions.show', $revision)->with('status', 'Revisi diperbarui');
    }

    public function destroy(Request $request, Revision $revision): RedirectResponse
    {
        $this->authorizeDosen($request->user()->id, $revision->dosen_id);
        $revision->delete();
        Log::info('revisions.destroy', ['user_id' => $request->user()->id, 'revision_id' => $revision->id]);
        return redirect()->route('revisions.index')->with('status', 'Revisi dihapus');
    }

    public function exportPDF(Request $request, Revision $revision): Response
    {
        $this->authorizeDosen($request->user()->id, $revision->dosen_id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('revisions.pdf', [
            'revision' => $revision->load(['mahasiswa', 'dosen']),
        ])->setPaper('a4');

        Log::info('revisions.export_pdf', ['user_id' => $request->user()->id, 'revision_id' => $revision->id]);

        return $pdf->download('revisi-'.$revision->id.'.pdf');
    }

    public function share(string $token): View
    {
        $revision = Revision::with(['mahasiswa', 'dosen'])->where('token', $token)->firstOrFail();
        return view('revisions.share', compact('revision'));
    }

    public function mahasiswaBimbingan(Request $request): View
    {
        // Get all mahasiswa yang dibimbing oleh dosen yang login
        $mahasiswaList = User::where('role', 'mahasiswa')
            ->where('dosen_pembimbing_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        // Pre-load semua revisi untuk semua mahasiswa sekaligus (menghindari N+1 query)
        $mahasiswaIds = $mahasiswaList->pluck('id');
        $allRevisions = Revision::whereIn('mahasiswa_id', $mahasiswaIds)
            ->with('dosen')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('mahasiswa_id');

        // Attach revisions dan hitung statistik untuk setiap mahasiswa
        $mahasiswaList->each(function ($mahasiswa) use ($allRevisions) {
            $revisions = $allRevisions->get($mahasiswa->id, collect());
            
            // Attach revisions collection
            $mahasiswa->revisions = $revisions;
            
            // Pre-calculate statistics (menghindari query di view)
            $mahasiswa->total_revisi = $revisions->count();
            $mahasiswa->revisi_belum_diperbaiki = $revisions->where('status', 'belum_diperbaiki')->count();
            $mahasiswa->revisi_sudah_diperbaiki = $revisions->where('status', 'sudah_diperbaiki')->count();
        });

        return view('revisions.mahasiswa-bimbingan', compact('mahasiswaList'));
    }

    public function updateStatus(Request $request, Revision $revision): RedirectResponse
    {
        $dosen = $request->user();
        
        // Verify that the logged-in user is either:
        // 1. The dosen who created this revision, OR
        // 2. The dosen pembimbing of the mahasiswa
        $isCreator = $dosen->id === $revision->dosen_id;
        $isPembimbing = $revision->mahasiswa->dosen_pembimbing_id === $dosen->id;
        
        if (!$isCreator && !$isPembimbing) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status revisi ini.');
        }

        if ($revision->status === 'belum_diperbaiki') {
            $revision->status = 'sudah_diperbaiki';
            $revision->save();

            Log::info('revisions.update_status', [
                'user_id' => $request->user()->id,
                'revision_id' => $revision->id,
                'new_status' => 'sudah_diperbaiki',
                'is_creator' => $isCreator,
                'is_pembimbing' => $isPembimbing
            ]);

            return redirect()->back()->with('success', 'Status revisi berhasil diperbarui menjadi sudah diperbaiki.');
        }

        return redirect()->back()->with('error', 'Status revisi tidak dapat diubah.');
    }

    private function authorizeDosen(int $currentUserId, int $dosenId): void
    {
        if ($currentUserId !== $dosenId) {
            abort(403);
        }
    }
}


