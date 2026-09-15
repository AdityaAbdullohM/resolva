<div class="question-form-group border p-4 rounded-lg mb-4 bg-gray-50">
    <h3 class="text-lg font-semibold mb-3">Pertanyaan Baru</h3>
    <div class="mb-3">
        <label for="question_text" class="block text-gray-700 text-sm font-bold mb-2">Teks Pertanyaan:</label>
        <textarea name="question_text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label for="question_type" class="block text-gray-700 text-sm font-bold mb-2">Tipe Pertanyaan:</label>
        <select name="question_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline question-type-select" required>
            <option value="multiple_choice">Pilihan Ganda</option>
            <option value="essay">Esai</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="points" class="block text-gray-700 text-sm font-bold mb-2">Poin:</label>
        <input type="number" name="points" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" value="1" required>
    </div>

    <div class="multiple-choice-options-container" style="display: none;">
        <h4 class="text-md font-semibold mb-2">Opsi Pilihan Ganda</h4>
        <div class="options-wrapper">
            <div class="option-item flex items-center mb-2">
                <input type="text" name="options[][answer_text]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2" placeholder="Teks Opsi" required>
                <input type="checkbox" name="options[][is_correct]" class="mr-2">
                <label class="text-gray-700 text-sm">Benar</label>
                <button type="button" class="remove-option-btn bg-red-400 hover:bg-red-600 text-white font-bold py-1 px-2 rounded text-sm ml-2">X</button>
            </div>
        </div>
        <button type="button" class="add-option-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Tambah Opsi</button>
    </div>

    <button type="button" class="remove-question-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-4">Hapus Pertanyaan</button>
</div>

<script>
    document.querySelectorAll('.question-type-select').forEach(selectElement => {
        function toggleOptions() {
            const optionsContainer = selectElement.closest('.question-form-group').querySelector('.multiple-choice-options-container');
            if (selectElement.value === 'multiple_choice') {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }

        selectElement.addEventListener('change', toggleOptions);
        toggleOptions(); // Initial call to set visibility based on default/old value
    });

    document.querySelectorAll('.add-option-btn').forEach(button => {
        button.addEventListener('click', function () {
            const optionsWrapper = this.closest('.multiple-choice-options-container').querySelector('.options-wrapper');
            const newOption = document.createElement('div');
            newOption.classList.add('option-item', 'flex', 'items-center', 'mb-2');
            newOption.innerHTML = `
                <input type="text" name="options[][answer_text]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2" placeholder="Teks Opsi" required>
                <input type="checkbox" name="options[][is_correct]" class="mr-2">
                <label class="text-gray-700 text-sm">Benar</label>
                <button type="button" class="remove-option-btn bg-red-400 hover:bg-red-600 text-white font-bold py-1 px-2 rounded text-sm ml-2">X</button>
            `;
            optionsWrapper.appendChild(newOption);
        });
    });

    document.querySelectorAll('.options-wrapper').forEach(wrapper => {
        wrapper.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-option-btn')) {
                event.target.closest('.option-item').remove();
            }
        });
    });
</script>