<?php

namespace Preferred\Domain\Users\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Preferred\Domain\Users\Entities\AuthorizedDevice;
use Preferred\Interfaces\Http\Controllers\Controller;

class AuthorizeDeviceController extends Controller
{
    public function authorizeDevice(string $token)
    {
        $authorizedDevice = AuthorizedDevice::with([])
            ->withoutGlobalScopes()
            ->where('authorization_token', '=', $token)
            ->first();

        if (!empty($authorizedDevice)) {
            if (empty($authorizedDevice->authorized_at)) {
                $authorizedDevice->update(['authorized_at' => now()->format('Y-m-d H:i:s')]);
            }

            $message = __('Device/location successfully authorized');

            return $this->respondWithCustomData(['message' => $message], Response::HTTP_OK);
        }

        $message = __('Invalid token for authorize new device/location');

        return $this->respondWithCustomData(['message' => $message], Response::HTTP_BAD_REQUEST);
    }

    public function destroy(string $id)
    {
        $model = AuthorizedDevice::with([])->findOrFail($id);

        try {
            $model->delete();

            return $this->respondWithNoContent();
        } catch (Exception $exception) {
            $message = __('Could not delete the authorized device');

            return $this->respondWithCustomData(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
