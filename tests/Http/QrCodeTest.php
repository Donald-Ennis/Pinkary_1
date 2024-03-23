<?php

declare(strict_types=1);

use App\Models\User;

test('can QR Code be downloaded only by authenticated users', function () {
    $response = $this->get(route('qr-code.download'));

    $response->assertRedirect(route('login'));
});

test('user can download qr code', function () {
    $user = User::factory()->create();

    $qrCode = QrCode::size(512)
        ->format('png')
        ->backgroundColor(3, 7, 18, 100)
        ->color(237, 163, 192, 100)
        ->merge('/public/img/ico.png')
        ->errorCorrection('M')
        ->generate(route('profile.show', $user));

    $response = $this->actingAs($user)->get(route('qr-code.download'));

    $response
        ->assertOk()
        ->assertStreamedContent($qrCode->toHtml())
        ->assertHeader('content-type', 'image/png')
        ->assertDownload('pinkary_'.$user->username.'.png');
});
