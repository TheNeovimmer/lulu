<?php
namespace Tests\Unit;

use App\Repositories\UserRepository;
use App\Services\AuthService;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase {
    private UserRepository $userRepo;
    private AuthService $service;

    protected function setUp(): void {
        $this->userRepo = $this->createMock(UserRepository::class);
        $this->service = new AuthService($this->userRepo);
    }

    public function testAuthenticateReturnsUserOnSuccess(): void {
        $hashed = password_hash('secret123', PASSWORD_DEFAULT);
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('alice@test.tn')
            ->willReturn(['id' => 1, 'email' => 'alice@test.tn', 'password' => $hashed, 'status' => 'active']);

        $result = $this->service->authenticate('alice@test.tn', 'secret123');
        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('error', $result);
        $this->assertSame(1, $result['id']);
    }

    public function testAuthenticateReturnsNullOnWrongPassword(): void {
        $hashed = password_hash('secret123', PASSWORD_DEFAULT);
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('alice@test.tn')
            ->willReturn(['id' => 1, 'email' => 'alice@test.tn', 'password' => $hashed, 'status' => 'active']);

        $result = $this->service->authenticate('alice@test.tn', 'wrongpassword');
        $this->assertNull($result);
    }

    public function testAuthenticateReturnsNullOnUnknownUser(): void {
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('unknown@test.tn')
            ->willReturn(null);

        $result = $this->service->authenticate('unknown@test.tn', 'password');
        $this->assertNull($result);
    }

    public function testAuthenticateReturnsErrorOnSuspendedAccount(): void {
        $hashed = password_hash('password', PASSWORD_DEFAULT);
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('bob@test.tn')
            ->willReturn(['id' => 2, 'email' => 'bob@test.tn', 'password' => $hashed, 'status' => 'suspended']);

        $result = $this->service->authenticate('bob@test.tn', 'password');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('suspendu', $result['error']);
    }

    public function testGetRedirectUrlReturnsCorrectPaths(): void {
        $this->assertSame('/admin', $this->service->getRedirectUrl('admin'));
        $this->assertSame('/expert/dashboard', $this->service->getRedirectUrl('expert'));
        $this->assertSame('/ctt/dashboard', $this->service->getRedirectUrl('ctt'));
        $this->assertSame('/dashboard', $this->service->getRedirectUrl('maman'));
        $this->assertSame('/dashboard', $this->service->getRedirectUrl('unknown'));
    }

    public function testRegisterReturnsNullOnDuplicateEmail(): void {
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('existing@test.tn')
            ->willReturn(['id' => 1, 'email' => 'existing@test.tn']);

        $result = $this->service->register('Test', 'existing@test.tn', 'password');
        $this->assertNull($result);
    }

    public function testRegisterReturnsNullWhenNoMamanRole(): void {
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('new@test.tn')
            ->willReturn(null);

        $this->userRepo->expects($this->once())
            ->method('getRoleIdBySlug')
            ->with('maman')
            ->willReturn(null);

        $result = $this->service->register('New', 'new@test.tn', 'password');
        $this->assertNull($result);
    }

    public function testRegisterCreatesUser(): void {
        $this->userRepo->expects($this->once())
            ->method('findByEmail')
            ->with('new@test.tn')
            ->willReturn(null);

        $this->userRepo->expects($this->once())
            ->method('getRoleIdBySlug')
            ->with('maman')
            ->willReturn(3);

        $this->userRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['name'] === 'New'
                    && $data['email'] === 'new@test.tn'
                    && $data['role_id'] === 3
                    && $data['status'] === 'active'
                    && password_verify('password', $data['password']);
            }))
            ->willReturn(10);

        $result = $this->service->register('New', 'new@test.tn', 'password');
        $this->assertSame(10, $result);
    }
}
