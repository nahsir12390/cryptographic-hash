<?php

namespace App\Http\Controllers;

use App\Models\HashInteraction;
use App\Services\HashVisualizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HashController extends Controller
{
    public function __construct(private readonly HashVisualizerService $hasher) {}

    public function index()
    {
        return view('hash.index', [
            'algorithms' => HashVisualizerService::ALGORITHMS,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:2000'],
            'algorithms' => ['required', 'array', 'min:1'],
            'algorithms.*' => [Rule::in(array_keys(HashVisualizerService::ALGORITHMS))],
        ]);

        $results = [];

        foreach (array_unique($data['algorithms']) as $algorithm) {
            $digest = $this->hasher->digest($algorithm, $data['text']);

            $results[$algorithm] = [
                'algorithm' => $algorithm,
                'label' => $this->hasher->info($algorithm)['label'],
                'digest' => $digest,
            ];

            HashInteraction::create([
                'action' => 'generate',
                'algorithm' => $algorithm,
                'input_preview' => mb_substr($data['text'], 0, 120),
                'hash_output' => $digest,
            ]);
        }

        return response()->json(['results' => $results]);
    }

    public function compare(Request $request): JsonResponse
    {
        $data = $request->validate([
            'text_a' => ['required', 'string', 'max:2000'],
            'text_b' => ['required', 'string', 'max:2000'],
            'algorithm' => ['required', Rule::in(array_keys(HashVisualizerService::ALGORITHMS))],
        ]);

        $algorithm = $data['algorithm'];
        $hashA = $this->hasher->digest($algorithm, $data['text_a']);
        $hashB = $this->hasher->digest($algorithm, $data['text_b']);

        $diff = $this->hasher->bitDifference($hashA, $hashB);
        $isCollision = $hashA === $hashB && $data['text_a'] !== $data['text_b'];

        HashInteraction::create([
            'action' => 'compare',
            'algorithm' => $algorithm,
            'input_preview' => mb_substr($data['text_a'], 0, 120),
            'second_input_preview' => mb_substr($data['text_b'], 0, 120),
            'hash_output' => $hashA,
            'second_hash_output' => $hashB,
            'differing_bits' => $diff['differing_bits'],
            'differing_percentage' => $diff['differing_percentage'],
            'is_collision' => $isCollision,
        ]);

        return response()->json([
            'algorithm' => $algorithm,
            'hash_a' => $hashA,
            'hash_b' => $hashB,
            'diff' => $diff,
            'is_collision' => $isCollision,
        ]);
    }
}
