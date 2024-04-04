<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('guest', function () {
    $response = $this->get(route('profile.edit'));

    $response->assertRedirect('/login');
});

test('auth', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertSee([
        'Profile Information',
        "Update your account's profile information and email address.",
        'Update Password',
        'Ensure your account is using a long, random password to stay secure.',
        'Delete Account',
        'Once your account is deleted, all of its resources and data will be permanently deleted.',
    ]);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'timezone' => 'UTC',
            'mail_preference_time' => 'daily',
            'prefers_anonymous_questions' => false,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertSame('testuser', $user->username);
    $this->assertNull($user->email_verified_at);
    $this->assertFalse($user->prefers_anonymous_questions);
});

test('username can be updated to uppercase', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => 'TESTUSER',
            'email' => $user->email,
            'timezone' => 'UTC',
            'mail_preference_time' => 'daily',
            'prefers_anonymous_questions' => false,
        ]);

    $response->assertSessionHasNoErrors();

    $this->get('/@testuser')->assertOk();
    $this->get('/@TESTUSER')->assertOk();
});

test('can not update to an existing username using uppercase', function () {
    User::factory()->create([
        'username' => 'testuser',
    ]);

    $user = User::factory()->create([
        'username' => 'testuser2',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => 'TESTUSER',
            'email' => $user->email,
            'timezone' => 'UTC',
            'mail_preference_time' => 'daily',
            'prefers_anonymous_questions' => true,
        ]);

    $response
        ->assertStatus(302)
        ->assertSessionHasErrors([
            'username' => 'The username has already been taken.',
        ]);

    $this->get('/@testuser')->assertOk();
    $this->get('/@TESTUSER')->assertOk();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => $user->email,
            'timezone' => 'UTC',
            'mail_preference_time' => 'daily',
            'prefers_anonymous_questions' => false,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('avatar is deleted when account is deleted', function () {
    $user = User::factory()->create([
        'avatar' => 'storage/avatars/default.png',
    ]);

    Storage::disk('public')->put('avatars/default.png', '...');

    Storage::disk('public')->assertExists('avatars/default.png');

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    Storage::disk('public')->assertMissing('avatars/default.png');

    $response->assertSessionHasNoErrors();

    $this->assertNull($user->fresh());
    $this->assertFileDoesNotExist(storage_path('app/public/avatars/default.png'));
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test("can not update user's name with blank characters", function () {
    $user = User::factory()->state(['name' => 'Test User'])->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => "\u{200E}",
        ]);

    $response->assertSessionHasErrors(['name' => 'The name field is required.']);

    $this->assertSame('Test User', $user->name);
});

test('prefers_anonymous_questions can be updated', function () {
    $user = User::factory()->create([
        'prefers_anonymous_questions' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => $user->email,
            'timezone' => 'UTC',
            'mail_preference_time' => 'daily',
            'prefers_anonymous_questions' => false,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect($user->refresh()->prefers_anonymous_questions)->toBeFalse();
});
