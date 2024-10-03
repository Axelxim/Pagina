document.addEventListener('DOMContentLoaded', function () {
    const addExerciseBtn = document.getElementById('addExerciseBtn');
    const ejerciciosContainer = document.getElementById('ejerciciosContainer');
    const savedExercisesContainer = document.getElementById('savedExercisesContainer');
    const saveRoutineBtn = document.getElementById('saveRoutineBtn');

    let ejerciciosGuardados = [];

    // Cargar usuarios
    fetch('get_users.php')
        .then(response => response.json())
        .then(data => {
            const userSelect = document.getElementById('userSelect');
            userSelect.innerHTML = '<option value="">Seleccionar usuario</option>';
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.nombres} ${user.apellido_paterno} ${user.apellido_materno}`; // Mostrar nombre completo
                userSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar usuarios:', error));

    // Cargar grupos musculares
    fetch('get_grupos_musculares.php')
        .then(response => response.json())
        .then(data => {
            addExerciseBtn.addEventListener('click', function () {
                const newExerciseGroup = document.createElement('div');
                newExerciseGroup.className = 'exercise-group';

                newExerciseGroup.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <label for="grupoMuscularSelect" class="form-label">Grupo Muscular</label>
                            <select class="form-select grupo-muscular">
                                <option value="">Seleccionar grupo muscular</option>
                            </select>

                            <label for="ejercicioSelect" class="form-label">Ejercicio</label>
                            <select class="form-select ejercicio">
                                <option value="">Selecciona ejercicio</option>
                            </select>

                            <div class="row mt-3">
                                <div class="col">
                                    <label for="repeticionesInput" class="form-label">Repeticiones</label>
                                    <input type="number" class="form-control repeticiones" placeholder="0" min="0">
                                </div>
                                <div class="col">
                                    <label for="seriesInput" class="form-label">Series</label>
                                    <input type="number" class="form-control series" placeholder="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        <button class="btn btn-primary btn-sm save-exercise">Guardar Ejercicio</button>
                        <button class="btn btn-danger btn-sm ms-2 delete-exercise">Eliminar</button>
                    </div>
                `;

                ejerciciosContainer.appendChild(newExerciseGroup);

                const grupoMuscularSelect = newExerciseGroup.querySelector('.grupo-muscular');
                const ejercicioSelect = newExerciseGroup.querySelector('.ejercicio');

                // Llenar el select de grupos musculares
                data.forEach(grupo => {
                    const option = document.createElement('option');
                    option.value = grupo.id;
                    option.textContent = grupo.nombre;
                    grupoMuscularSelect.appendChild(option);
                });

                // Evento para cargar ejercicios al seleccionar un grupo muscular
                grupoMuscularSelect.addEventListener('change', function () {
                    const selectedGroupId = this.value;
                    ejercicioSelect.innerHTML = '<option value="">Selecciona ejercicio</option>'; // Limpiar opciones anteriores

                    if (selectedGroupId) {
                        fetch(`get_ejercicios.php?grupoMuscularId=${selectedGroupId}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(ejercicio => {
                                    const option = document.createElement('option');
                                    option.value = ejercicio.id; // Guardamos el ID del ejercicio
                                    option.textContent = ejercicio.nombre; // Mostramos el nombre del ejercicio
                                    ejercicioSelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error al cargar ejercicios:', error));
                    }
                });

                const deleteBtn = newExerciseGroup.querySelector('.delete-exercise');
                deleteBtn.addEventListener('click', function () {
                    ejerciciosContainer.removeChild(newExerciseGroup);
                });

                const saveBtn = newExerciseGroup.querySelector('.save-exercise');
                saveBtn.addEventListener('click', function () {
                    const grupoMuscularId = grupoMuscularSelect.value;
                    const grupoMuscularNombre = grupoMuscularSelect.options[grupoMuscularSelect.selectedIndex].text; // Obtener el nombre del grupo muscular
                    const ejercicioId = ejercicioSelect.value;
                    const ejercicioNombre = ejercicioSelect.options[ejercicioSelect.selectedIndex].text; // Obtener el nombre del ejercicio
                    const repeticiones = newExerciseGroup.querySelector('.repeticiones').value;
                    const series = newExerciseGroup.querySelector('.series').value;

                    if (grupoMuscularId && ejercicioId && repeticiones && series) {
                        const nuevoEjercicio = {
                            grupoMuscular: grupoMuscularNombre, // Guardamos el nombre del grupo muscular
                            ejercicio: ejercicioNombre, // Guardamos el nombre del ejercicio
                            repeticiones: parseInt(repeticiones), // Asegúrate de convertir a número
                            series: parseInt(series) // Asegúrate de convertir a número
                        };

                        ejerciciosGuardados.push(nuevoEjercicio);

                        const savedExercise = document.createElement('div');
                        savedExercise.className = 'exercise-group mt-3';

                        savedExercise.innerHTML = `
                            <div>
                                <strong>Grupo Muscular:</strong> ${grupoMuscularNombre} <br>
                                <strong>Ejercicio:</strong> ${ejercicioNombre} <br>
                                <strong>Repeticiones:</strong> ${repeticiones} <br>
                                <strong>Series:</strong> ${series}
                            </div>
                            <div class="mt-2 d-flex justify-content-end">
                                <button class="btn btn-warning btn-sm edit-exercise">Editar</button>
                                <button class="btn btn-danger btn-sm ms-2 delete-exercise">Eliminar</button>
                            </div>
                        `;

                        savedExercisesContainer.appendChild(savedExercise);
                        ejerciciosContainer.removeChild(newExerciseGroup);

                        const editBtn = savedExercise.querySelector('.edit-exercise');
                        editBtn.addEventListener('click', function () {
                            // Aquí puedes implementar la lógica de edición si es necesario
                        });

                        const savedDeleteBtn = savedExercise.querySelector('.delete-exercise');
                        savedDeleteBtn.addEventListener('click', function () {
                            savedExercisesContainer.removeChild(savedExercise);
                        });

                        // Actualizar el identificador de rutina
                        document.getElementById('rutinaIdentificador').style.display = 'block';
                        const identificadorRutina = `RTN-${ejerciciosGuardados.length}`; // Cambiar el formato del ID de rutina
                        document.getElementById('identificadorRutina').textContent = identificadorRutina;
                    } else {
                        alert('Por favor, completa todos los campos.');
                    }
                });
            });
        })
        .catch(error => console.error('Error al cargar grupos musculares:', error));

    // Guardar rutina
    saveRoutineBtn.addEventListener('click', function () {
        const userId = document.getElementById('userSelect').value;

        if (userId && ejerciciosGuardados.length > 0) {
            fetch('save_routine.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: userId,
                    ejercicios: ejerciciosGuardados
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Rutina guardada exitosamente.');
                    // Limpiar el formulario si se desea
                    document.getElementById('userSelect').value = '';
                    savedExercisesContainer.innerHTML = '';
                    ejerciciosGuardados = [];
                    document.getElementById('rutinaIdentificador').style.display = 'none';
                } else {
                    alert('Error al guardar la rutina: ' + data.message);
                }
            })
            .catch(error => console.error('Error al guardar rutina:', error));
        } else {
            alert('Selecciona un usuario y agrega al menos un ejercicio.');
        }
    });
});
