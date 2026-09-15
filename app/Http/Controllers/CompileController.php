<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JdoodleService;
use Illuminate\Http\JsonResponse;

class CompileController extends Controller
{
    protected JdoodleService $jdoodle;

    public function __construct(JdoodleService $jdoodle)
    {
        $this->jdoodle = $jdoodle;
    }

    public function run(Request $request): JsonResponse
    {
        $data = $request->validate([
            'script' => 'required|string',
            'language' => 'sometimes|string',
            'versionIndex' => 'sometimes|string',
        ]);

        $language = $data['language'] ?? 'php';
        $versionIndex = $data['versionIndex'] ?? '0';

        $result = $this->jdoodle->execute($data['script'], $language, $versionIndex);

        return response()->json($result);
    }
}
