<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Folder tujuan unggahan modul Alur Prosedur.
 *
 * Modulnya sudah lama ada di panel dengan `upload: { folder: 'alur-prosedur' }`,
 * tetapi foldernya tidak pernah didaftarkan pada daftar putih
 * `UploadController::FOLDER`. Akibatnya setiap unggahan dijawab "Pilihan folder
 * tidak sah." dan halaman Standar Layanan tidak pernah punya gambar untuk
 * ditayangkan.
 *
 * Daftar putihnya tetap ketat — itu yang menahan path traversal lewat isian —
 * jadi tes ini menjaga dua sisi sekaligus: folder modul yang sah diterima, dan
 * nama folder karangan tetap ditolak.
 */
class UnggahAlurProsedurTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ppid.akun.recaptcha_aktif' => false,
            'ppid.akun.gagal_per_tahap' => 99,
        ]);

        Storage::fake('media');
    }

    private function token(): string
    {
        $tanda = Str::random(8);

        $user = User::create([
            'name' => "Uji Unggah $tanda",
            'email' => 'uji.unggah.'.Str::lower($tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', 'super-admin')->value('id'),
            'is_active' => true,
        ]);

        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    public function test_folder_alur_prosedur_diterima(): void
    {
        $this->withToken($this->token())
            ->postJson('/api/v1/uploads', [
                'folder' => 'alur-prosedur',
                'jenis' => 'gambar',
                'file' => UploadedFile::fake()->image('alur-1.png', 800, 600),
            ])
            ->assertCreated()
            ->assertJsonPath('data.path', fn ($path) => Str::contains((string) $path, 'alur-prosedur'));
    }

    public function test_folder_karangan_tetap_ditolak(): void
    {
        $this->withToken($this->token())
            ->postJson('/api/v1/uploads', [
                'folder' => '../../etc',
                'jenis' => 'gambar',
                'file' => UploadedFile::fake()->image('alur-1.png'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('folder');
    }
}
