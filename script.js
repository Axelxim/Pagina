document.addEventListener('DOMContentLoaded', function () {
    const addExerciseBtn = document.getElementById('addExerciseBtn');
    const ejerciciosContainer = document.getElementById('ejerciciosContainer');
    const savedExercisesContainer = document.getElementById('savedExercisesContainer');
    const saveRoutineBtn = document.getElementById('saveRoutineBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const clearSummaryBtn = document.getElementById('clearSummaryBtn');
    const summaryBody = document.getElementById('summaryBody');
    const summaryContainer = document.getElementById('summaryContainer');

    let ejerciciosGuardados = []; // Arreglo para guardar ejercicios

    // Cargar usuarios
    fetch('get_users.php')
        .then(response => response.json())
        .then(data => {
            const userSelect = document.getElementById('userSelect');
            userSelect.innerHTML = '<option value="">Seleccionar usuario</option>';
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.nombres} ${user.apellido_paterno} ${user.apellido_materno}`;
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
                            grupoMuscular: grupoMuscularNombre,
                            ejercicio: ejercicioNombre,
                            repeticiones: parseInt(repeticiones),
                            series: parseInt(series)
                        };

                        // Guardar ejercicio en el array global
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

                        // Limpiar inputs
                        grupoMuscularSelect.value = '';
                        ejercicioSelect.value = '';
                        newExerciseGroup.querySelector('.repeticiones').value = '';
                        newExerciseGroup.querySelector('.series').value = '';

                        console.log('Ejercicios guardados actualizados:', ejerciciosGuardados); // Log para verificar

                        const editBtn = savedExercise.querySelector('.edit-exercise');
                        editBtn.addEventListener('click', function () {
                            // Rellenar el formulario con los valores actuales del ejercicio
                            grupoMuscularSelect.value = grupoMuscularId; // Reasignar el ID del grupo muscular
                            ejercicioSelect.value = ejercicioId; // Reasignar el ID del ejercicio
                            newExerciseGroup.querySelector('.repeticiones').value = repeticiones; // Rellenar repeticiones
                            newExerciseGroup.querySelector('.series').value = series; // Rellenar series

                            // Eliminar el ejercicio guardado para reemplazarlo
                            savedExercisesContainer.removeChild(savedExercise);
                            ejerciciosGuardados = ejerciciosGuardados.filter(ejercicio => ejercicio.ejercicio !== ejercicioNombre); // Eliminar del array

                            // Reagregar el grupo muscular para poder guardar nuevamente
                            ejerciciosContainer.appendChild(newExerciseGroup);
                        });

                        const savedDeleteBtn = savedExercise.querySelector('.delete-exercise');
                        savedDeleteBtn.addEventListener('click', function () {
                            savedExercisesContainer.removeChild(savedExercise);
                            // Eliminar del array ejerciciosGuardados
                            ejerciciosGuardados = ejerciciosGuardados.filter(ejercicio => ejercicio.ejercicio !== ejercicioNombre);
                            console.log('Ejercicios guardados después de eliminar:', ejerciciosGuardados); // Log después de eliminar
                        });
                    } else {
                        alert('Por favor, completa todos los campos.');
                    }
                });
            });
        })
        .catch(error => console.error('Error al cargar grupos musculares:', error));

    // Guardar rutina
    // Reemplaza el código de guardar rutina existente con este
saveRoutineBtn.addEventListener('click', function () {
    const userId = document.getElementById('userSelect').value;
    const userSelect = document.getElementById('userSelect');
    const selectedUserName = userSelect.options[userSelect.selectedIndex].text; // Obtener el nombre del usuario seleccionado

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
            console.log('Respuesta del servidor al guardar rutina:', data); // Verifica la respuesta
            if (data.success) {
                alert('Rutina guardada exitosamente.');

                // Generar el identificador de la rutina
                const identificadorRutina = `Rutina ${data.rutinaId}`; // Asegúrate de que 'rutinaId' esté en la respuesta
                document.getElementById('rutinaTitle').textContent = identificadorRutina;

                // Obtener ejercicios desde la base de datos
                fetch(`get_rutina.php?rutinaId=${data.rutinaId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            summaryBody.innerHTML = '';

                            // Añadir nombre del usuario al resumen
                            const userRow = document.createElement('tr');
                            userRow.innerHTML = `
                                <td colspan="5"><strong>Usuario:</strong> ${selectedUserName}</td>
                            `;
                            summaryBody.appendChild(userRow);

                            data.ejercicios.forEach((ejercicio, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `

                                    <td>${ejercicio.grupo_muscular}</td>
                                    <td>${ejercicio.ejercicio}</td>
                                    <td>${ejercicio.repeticiones}</td>
                                    <td>${ejercicio.series}</td>
                                `;
                                summaryBody.appendChild(row);
                            });
                            summaryContainer.style.display = 'block'; // Mostrar resumen
                        } else {
                            alert('Error al recuperar los ejercicios de la rutina.');
                        }
                    })
                    .catch(error => console.error('Error al obtener rutina:', error));
                
                // Limpiar la lista de ejercicios guardados después de guardar la rutina
                savedExercisesContainer.innerHTML = '';
                ejerciciosGuardados = []; // Limpiar ejercicios guardados
            } else {
                alert('Error al guardar la rutina.');
            }
        })
        .catch(error => console.error('Error al guardar rutina:', error));
    } else {
        alert('Por favor, selecciona un usuario y añade ejercicios a la rutina.');
    }
});


// Exportar a PDF
exportPdfBtn.addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Título del PDF
    doc.setFontSize(18);
    doc.text('Resumen de Rutina', 14, 20);
    
    // Agregar el nombre del usuario
    const selectedUserName = document.getElementById('userSelect').options[document.getElementById('userSelect').selectedIndex].text; // Obtener el nombre del usuario seleccionado
    doc.setFontSize(12);
    doc.text(`Usuario: ${selectedUserName}`, 14, 30);
    
    // Encabezados de la tabla
    const headers = [["Grupo Muscular", "Ejercicio", "Repeticiones", "Series"]];
    const rows = [];

    // Recopilar datos de la tabla de resumen
    const summaryRows = summaryBody.getElementsByTagName('tr');
    for (let i = 1; i < summaryRows.length; i++) { // Cambia a 1 para omitir la primera fila (nombre del usuario)
        const cols = summaryRows[i].getElementsByTagName('td');
        if (cols.length > 0) {
            const rowData = [];
            for (let j = 0; j < cols.length; j++) {
                rowData.push(cols[j].innerText);
            }
            rows.push(rowData);
        }
    }

    // Añadir encabezados y filas al PDF
    doc.autoTable({
        head: headers,
        body: rows,
        startY: 40,
        margin: { horizontal: 10 },
    });

    // Guardar el PDF
    doc.save('rutina.pdf');
});


    // Limpiar resumen
    clearSummaryBtn.addEventListener('click', function () {
        summaryBody.innerHTML = '';
        summaryContainer.style.display = 'none'; // Ocultar resumen
        document.getElementById('userSelect').selectedIndex = 0; // Reiniciar selección de usuario
    });
});
