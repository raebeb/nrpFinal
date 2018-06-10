# Comandos utiles

sudo a2ensite production
sudo a2ensite nrp
sudo a2dissite <version>


# Cambios

5.1.1 - 31/01/2018
Se modifica la lectura del archivo general.csv
Se agrega un seeder para datos de prueba
Se agrega un event (FileWasUploaded) y un listener (GenerateSchedule)

5.1.2 - 06/02/2018
Se elimina el event (FileWasUploaded) y el listener (GenerateSchedule). Problemas al redireccionar desde el evento.
Se agrega la politica HistoryCsvPolicy. Cada autor solo puede acceder a sus planificaciones.
Se optimizan las consultas DB con eloquent.
Se agregan las últimas planificaciones en la vista Home, disponible para autor, moderador y administrador.
Se agrega el menú Historial CSV para los usuarios autor, moderador, administrador.
Se identifica una vulnerabilidad al editar el nombre de un usuario de tipo autor ya que afecta la tabla de planificaciones.

5.1.3 - 19-02-2018
Correcciones menores en las vistas layouts.app, moderador.historialcsv
Se agregan las funciones ucwords() strtolower() en UsersController.store en los campos name y lastname
Se bloquean las multisesiones

5.1.4 - 23-02-2018
Se agrega un sticky footer para colocar el logo de las instituciones que forman parte del proyecto
Se agrega la opcion de modificar el csv. Para ello se ha utilizado una tabla en html con su correspondiente vista (ManageSchedules.create.edit.blade), archivo js (d3.v3.min.js, editCsvForm.js). Un evento y listener (CsvWasReceived, StorageCsv).

5.1.5 - 27-02-2018
Se crea FormsController, PublicFormsController, manageForms (view), para administratar los formularios utilizados por la enfermera jefe.

5.1.6 - 5/03/2018
Se corrige el formulario (html, js, css) que permite editar el input. Se validan los campos de dicho formulario.

5.1.6.1 - 4/04/2018
Se cambia el input text por checkbox (manageSchedules.create.edit, public.js.editCsvForm.js)

cd nrp && sudo chown -R www-data: storage && sudo chmod -R 755 storage && cd storage/app/public
