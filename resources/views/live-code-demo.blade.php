<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Live Code Demo (Java)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="editor" style="height: 400px; width: 100%; border: 1px solid #ccc;"></div>
                    <button id="runButton" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Run Code</button>
                    <div class="mt-4">
                        <h3>Output:</h3>
                        <pre id="output" class="bg-gray-800 text-white p-4 rounded"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/monaco/vs/loader.js') }}"></script>
        <script>
            require.config({ paths: { 'vs': '{{ asset('js/monaco/vs') }}' }});
            require(['vs/editor/editor.main'], function () {
                var editor = monaco.editor.create(document.getElementById('editor'), {
                    value: [
                        'public class Main {',
                        '    public static void main(String[] args) {',
                        '        System.out.println("Hello, Monaco Editor!");',
                        '    }',
                        '}'
                    ].join('\n'),
                    language: 'java',
                    theme: 'vs-light'
                });

                document.getElementById('runButton').addEventListener('click', function() {
                    const code = editor.getValue();
                    document.getElementById('output').textContent = 'Running code...';

                    // Simulate API call for code execution
                    fetch('/api/java/run', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ code: code })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const outputElement = document.getElementById('output');
                        outputElement.textContent = ''; // Clear previous output
                        outputElement.classList.remove('text-red-500'); // Remove error styling

                        if (data.status === 'error') {
                            outputElement.textContent = data.error;
                            outputElement.classList.add('text-red-500'); // Add error styling
                        } else {
                            outputElement.textContent = data.output;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const outputElement = document.getElementById('output');
                        outputElement.textContent = 'Error running code. Please check console for details.';
                        outputElement.classList.add('text-red-500');
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
