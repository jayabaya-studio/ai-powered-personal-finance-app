<?php
// File: app/Http/Controllers/User/AccountController.php (Diperbarui)

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Services\AccountService;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
        // Terapkan AccountPolicy ke semua aksi resource secara default
        $this->authorizeResource(Account::class, 'account');
    }

    /**
     * Menampilkan daftar semua rekening yang dapat diakses pengguna.
     * Termasuk akun pribadi dan akun bersama dari Family Space yang aktif.
     * Juga dapat menampilkan formulir edit jika ada ID akun yang dikirim melalui sesi.
     */
    public function index(Request $request)
    {
        // Menggunakan getAccountsForCurrentUser untuk mendapatkan semua akun yang relevan
        $accounts = $this->accountService->getAccountsForCurrentUser();
        $editAccount = null;

        // Jika ada 'edit_id' di query string, coba temukan akun untuk diedit
        if ($request->has('edit_id')) {
            try {
                // Gunakan findById yang sudah diperbarui di AccountService
                $editAccount = $this->accountService->findById((int)$request->edit_id);
                // Policy 'view' sudah diaplikasikan oleh findById, tapi bisa juga diulang
                // $this->authorize('view', $editAccount);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                // Akun tidak ditemukan atau tidak diotorisasi
                return redirect()->route('accounts.index')->with('error', 'Account not found or unauthorized.');
            }
        }

        return view('user.accounts.index', compact('accounts', 'editAccount'));
    }

    /**
     * Menyimpan rekening baru ke database.
     */
    public function store(StoreAccountRequest $request)
    {
        // Policy 'create' sudah diaplikasikan oleh authorizeResource di konstruktor.
        // Tidak perlu $this->authorize('create', Account::class); secara eksplisit jika menggunakan authorizeResource.

        // Jika request memiliki is_joint dan user memiliki current_family_id, AccountService akan menanganinya
        $this->accountService->createAccount($request->validated());
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit rekening.
     * Ini akan me-redirect kembali ke halaman index dengan akun yang akan diedit.
     */
    public function edit(Account $account)
    {
        // Policy 'view' untuk model binding sudah diaplikasikan oleh authorizeResource.
        // Maka $account yang masuk ke sini sudah pasti diotorisasi.
        return redirect()->route('accounts.index', ['edit_id' => $account->id]);
    }

    /**
     * Memperbarui rekening yang sudah ada.
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        // Policy 'update' sudah diaplikasikan oleh authorizeResource di konstruktor.
        // $this->authorize('update', $account); // Hanya diperlukan jika tidak menggunakan authorizeResource

        $this->accountService->updateAccount($account->id, $request->validated());
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil diperbarui!');
    }

    /**
     * Menghapus rekening dari database.
     */
    public function destroy(Account $account)
    {
        // Policy 'delete' sudah diaplikasikan oleh authorizeResource di konstruktor.
        // $this->authorize('delete', $account); // Hanya diperlukan jika tidak menggunakan authorizeResource

        $this->accountService->deleteAccount($account->id);
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus!');
    }
}
