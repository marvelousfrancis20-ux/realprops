<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateAgentProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('agent.profile', [
            'profile' => $request->user()->ensureAgentProfile()->loadMissing('user'),
        ]);
    }

    public function update(UpdateAgentProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->ensureAgentProfile();

        $data = $request->validated();
        $idDocument = $data['id_document'] ?? null;
        $licenseDocument = $data['license_document'] ?? null;
        unset($data['id_document'], $data['license_document']);

        $profile->fill($data)->save();

        if ($idDocument) {
            $profile->clearMediaCollection('id_document');
            $profile->addMedia($idDocument)->toMediaCollection('id_document');
            $profile->forceFill(['id_document_path' => $profile->getFirstMediaUrl('id_document')])->saveQuietly();
        }

        if ($licenseDocument) {
            $profile->clearMediaCollection('license_document');
            $profile->addMedia($licenseDocument)->toMediaCollection('license_document');
            $profile->forceFill(['license_document_path' => $profile->getFirstMediaUrl('license_document')])->saveQuietly();
        }

        if ($request->filled('whatsapp')) {
            $user->forceFill(['whatsapp' => $request->input('whatsapp')])->saveQuietly();
        }

        return redirect()->route('agent.profile.edit')->with('status', 'Agency profile updated.');
    }
}
