document.addEventListener('DOMContentLoaded', function () {
    const addExerciseBtn = document.getElementById('addExerciseBtn');
    const ejerciciosContainer = document.getElementById('ejerciciosContainer');
    const savedExercisesContainer = document.getElementById('savedExercisesContainer');

    // Array para almacenar los ejercicios guardados
    let ejerciciosGuardados = [];

    // Evento para agregar un nuevo ejercicio
    addExerciseBtn.addEventListener('click', function () {
        const newExerciseGroup = document.createElement('div');
        newExerciseGroup.className = 'exercise-group';

        newExerciseGroup.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label for="grupoMuscularSelect" class="form-label">Grupo Muscular</label>
                    <select class="form-select grupo-muscular">
                        <option selected>Selecciona</option>
                        <option value="Pecho">Pecho</option>
                        <option value="Espalda">Espalda</option>
                        <option value="Tríceps">Tríceps</option>
                    </select>

                    <label for="ejercicioSelect" class="form-label">Ejercicio</label>
                    <select class="form-select ejercicio">
                        <option selected>Selecciona</option>
                    </select>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="repeticionesInput" class="form-label">Repeticiones</label>
                            <input type="number" class="form-control repeticiones" placeholder="0">
                        </div>
                        <div class="col">
                            <label for="seriesInput" class="form-label">Series</label>
                            <input type="number" class="form-control series" placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Botón de eliminar ejercicio -->
                <button class="btn btn-danger btn-sm delete-exercise">Eliminar</button>
            </div>
        `;

        // Agregar el nuevo grupo al contenedor
        ejerciciosContainer.appendChild(newExerciseGroup);

        // Evento para eliminar ejercicio de la interfaz
        const deleteBtn = newExerciseGroup.querySelector('.delete-exercise');
        deleteBtn.addEventListener('click', function () {
            ejerciciosContainer.removeChild(newExerciseGroup);
        });

        // Población de ejercicios basada en el grupo muscular
        const grupoMuscularSelect = newExerciseGroup.querySelector('.grupo-muscular');
        const ejercicioSelect = newExerciseGroup.querySelector('.ejercicio');
        
        grupoMuscularSelect.addEventListener('change', function () {
            const selectedGroup = this.value;
            populateExercises(ejercicioSelect, selectedGroup);
        });

        // Almacenar el ejercicio en el pseudo guardado y eliminar campos de edición
        const saveBtn = document.createElement('button');
        saveBtn.textContent = 'Guardar Ejercicio';
        saveBtn.className = 'btn btn-primary btn-sm';
        newExerciseGroup.appendChild(saveBtn);

        saveBtn.addEventListener('click', function () {
            const grupoMuscular = grupoMuscularSelect.value;
            const ejercicio = ejercicioSelect.value;
            const repeticiones = newExerciseGroup.querySelector('.repeticiones').value;
            const series = newExerciseGroup.querySelector('.series').value;

            // Validación básica
            if (grupoMuscular !== 'Selecciona' && ejercicio !== 'Selecciona' && repeticiones && series) {
                const nuevoEjercicio = {
                    grupoMuscular,
                    ejercicio,
                    repeticiones,
                    series
                };

                // Agregar el ejercicio al array
                ejerciciosGuardados.push(nuevoEjercicio);

                // Eliminar los campos de edición y reemplazarlos con texto
                newExerciseGroup.innerHTML = `
                    <div>
                        <strong>Grupo Muscular:</strong> ${grupoMuscular} <br>
                        <strong>Ejercicio:</strong> ${ejercicio} <br>
                        <strong>Repeticiones:</strong> ${repeticiones} <br>
                        <strong>Series:</strong> ${series}
                    </div>
                    <button class="btn btn-warning btn-sm edit-exercise">Editar</button>
                    <button class="btn btn-danger btn-sm delete-exercise">Eliminar</button>
                `;

                // Evento para editar el ejercicio
                const editBtn = newExerciseGroup.querySelector('.edit-exercise');
                editBtn.addEventListener('click', function () {
                    editarEjercicio(newExerciseGroup, nuevoEjercicio);
                });

                // Evento para eliminar el ejercicio guardado
                const deleteGuardadoBtn = newExerciseGroup.querySelector('.delete-exercise');
                deleteGuardadoBtn.addEventListener('click', function () {
                    eliminarEjercicio(newExerciseGroup, nuevoEjercicio);
                });
            } else {
                alert('Por favor, completa todos los campos antes de guardar.');
            }
        });
    });

    // Función para llenar el select de ejercicios basado en el grupo muscular
    function populateExercises(selectElement, group) {
        selectElement.innerHTML = ''; // Limpiar las opciones anteriores
        const defaultOption = document.createElement('option');
        defaultOption.textContent = 'Selecciona';
        selectElement.appendChild(defaultOption);

        let exercises = [];
        if (group === 'Pecho') {
            exercises = ['Press de Banca', 'Flexiones', 'Aperturas'];
        } else if (group === 'Espalda') {
            exercises = ['Dominadas', 'Remo', 'Peso Muerto'];
        } else if (group === 'Tríceps') {
            exercises = ['Fondos', 'Extensiones de Tríceps', 'Patadas de Tríceps'];
        }

        exercises.forEach(exercise => {
            const option = document.createElement('option');
            option.value = exercise;
            option.textContent = exercise;
            selectElement.appendChild(option);
        });
    }

    // Función para editar el ejercicio
    function editarEjercicio(exerciseGroup, ejercicio) {
        exerciseGroup.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label for="grupoMuscularSelect" class="form-label">Grupo Muscular</label>
                    <select class="form-select grupo-muscular">
                        <option value="Pecho" ${ejercicio.grupoMuscular === 'Pecho' ? 'selected' : ''}>Pecho</option>
                        <option value="Espalda" ${ejercicio.grupoMuscular === 'Espalda' ? 'selected' : ''}>Espalda</option>
                        <option value="Tríceps" ${ejercicio.grupoMuscular === 'Tríceps' ? 'selected' : ''}>Tríceps</option>
                    </select>

                    <label for="ejercicioSelect" class="form-label">Ejercicio</label>
                    <select class="form-select ejercicio">
                        <option value="${ejercicio.ejercicio}" selected>${ejercicio.ejercicio}</option>
                    </select>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="repeticionesInput" class="form-label">Repeticiones</label>
                            <input type="number" class="form-control repeticiones" value="${ejercicio.repeticiones}">
                        </div>
                        <div class="col">
                            <label for="seriesInput" class="form-label">Series</label>
                            <input type="number" class="form-control series" value="${ejercicio.series}">
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Volver a configurar el evento de guardar
        const saveBtn = document.createElement('button');
        saveBtn.textContent = 'Guardar Cambios';
        saveBtn.className = 'btn btn-success btn-sm mt-2';
        exerciseGroup.appendChild(saveBtn);

        saveBtn.addEventListener('click', function () {
            const updatedGrupoMuscular = exerciseGroup.querySelector('.grupo-muscular').value;
            const updatedEjercicio = exerciseGroup.querySelector('.ejercicio').value;
            const updatedRepeticiones = exerciseGroup.querySelector('.repeticiones').value;
            const updatedSeries = exerciseGroup.querySelector('.series').value;

            // Actualiza el objeto de ejercicio
            ejercicio.grupoMuscular = updatedGrupoMuscular;
            ejercicio.ejercicio = updatedEjercicio;
            ejercicio.repeticiones = updatedRepeticiones;
            ejercicio.series = updatedSeries;

            // Reemplaza con los datos actualizados
            exerciseGroup.innerHTML = `
                <div>
                    <strong>Grupo Muscular:</strong> ${updatedGrupoMuscular} <br>
                    <strong>Ejercicio:</strong> ${updatedEjercicio} <br>
                    <strong>Repeticiones:</strong> ${updatedRepeticiones} <br>
                    <strong>Series:</strong> ${updatedSeries}
                </div>
                <button class="btn btn-warning btn-sm edit-exercise">Editar</button>
                <button class="btn btn-danger btn-sm delete-exercise">Eliminar</button>
            `;

            // Restablece los eventos para editar y eliminar
            const editBtn = exerciseGroup.querySelector('.edit-exercise');
            editBtn.addEventListener('click', function () {
                editarEjercicio(exerciseGroup, ejercicio);
            });

            const deleteGuardadoBtn = exerciseGroup.querySelector('.delete-exercise');
            deleteGuardadoBtn.addEventListener('click', function () {
                eliminarEjercicio(exerciseGroup, ejercicio);
            });
        });
    }

    // Función para eliminar ejercicio
    function eliminarEjercicio(exerciseGroup, ejercicio) {
        // Elimina el ejercicio del array
        ejerciciosGuardados = ejerciciosGuardados.filter(e => e !== ejercicio);
        // Elimina el elemento del DOM
        exerciseGroup.remove();
    }
});
