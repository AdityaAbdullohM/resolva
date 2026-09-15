<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JavaCodeController extends Controller
{
    public function run(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->input('code');
        $output = '';
        $error = '';

        // --- Simulated Java Code Execution Logic ---
        // In a real scenario, this would involve:
        // 1. Saving the code to a temporary .java file.
        // 2. Compiling the .java file using `javac`.
        // 3. Running the compiled .class file using `java`.
        // 4. Capturing stdout and stderr.
        // 5. Handling timeouts and security sandboxing.

        // For this simulation, let's check for a simple pattern
        if (str_contains($code, 'public static void main(String[] args)')) {
            if (str_contains($code, 'System.out.println("Hello, Monaco Editor!");')) {
                $output = "Hello, Monaco Editor!\n(Simulated output)";
            } elseif (str_contains($code, 'throw new RuntimeException')) {
                $error = "Simulated Runtime Exception: An error occurred during execution.\n(Simulated error)";
            } elseif (str_contains($code, ';')) { // Basic check for valid statements
                $output = "Code executed successfully.\n(Simulated output)";
            } else {
                $error = "Simulated Compilation Error: Missing semicolon or invalid syntax.\n(Simulated error)";
            }
        } else {
            $error = "Simulated Compilation Error: Missing main method or class structure.\n(Simulated error)";
        }
        // --- End Simulated Logic ---

        return response()->json([
            'output' => $output,
            'error' => $error,
            'simulated' => true,
            'status' => $error ? 'error' : 'success',
        ]);
    }
}
