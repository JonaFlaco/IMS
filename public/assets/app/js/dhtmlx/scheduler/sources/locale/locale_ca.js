/*

@license
dhtmlxScheduler v.5.3.10 Professional

This software is covered by DHTMLX Commercial License. Usage without proper license is prohibited.

(c) XB Software Ltd.

*/
Scheduler.plugin(function(scheduler){/*
 @Traducido por Vicente Adria Bohigues - vicenteadria@hotmail.com
 */
scheduler.locale = {
	date: {
		month_full: ["Gener", "Febrer", "Març", "Abril", "Maig", "Juny", "Juliol", "Agost", "Setembre", "Octubre", "Novembre", "Desembre"],
		month_short: ["Gen", "Feb", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Des"],
		day_full: ["Diumenge", "Dilluns", "Dimarts", "Dimecres", "Dijous", "Divendres", "Dissabte"],
		day_short: ["Dg", "Dl", "Dm", "Dc", "Dj", "Dv", "Ds"]
	},
	labels: {
		dhx_cal_today_button: "Hui",
		day_tab: "Dia",
		week_tab: "Setmana",
		month_tab: "Mes",
		new_event: "Nou esdeveniment",
		icon_save: "Guardar",
		icon_cancel: "Cancel·lar",
		icon_details: "Detalls",
		icon_edit: "Editar",
		icon_delete: "Esborrar",
		confirm_closing: "", //"Els seus canvis es perdràn, continuar ?"
		confirm_deleting: "L'esdeveniment s'esborrarà definitivament, continuar ?",
		section_description: "Descripció",
		section_time: "Periode de temps",
		full_day: "Tot el dia",

		confirm_recurring: "¿Desitja modificar el conjunt d'esdeveniments repetits?",
		section_recurring: "Repeteixca l'esdeveniment",
		button_recurring: "Impedit",
		button_recurring_open: "Permés",
		button_edit_series: "Edit sèrie",
		button_edit_occurrence: "Edita Instància",

		/*agenda view extension*/
		agenda_tab: "Agenda",
		date: "Data",
		description: "Descripció",

		/*year view extension*/
		year_tab: "Any",

		/*week agenda view extension*/
		week_agenda_tab: "Agenda",

		/*grid view extension*/
		grid_tab: "Taula",

		/* touch tooltip*/
		drag_to_create:"Drag to create",
		drag_to_move:"Drag to move",

		/* dhtmlx message default buttons */
		message_ok:"OK",
		message_cancel:"Cancel",

		/* wai aria labels for non-text controls */
		next: "Next",
		prev: "Previous",
		year: "Year",
		month: "Month",
		day: "Day",
		hour:"Hour",
		minute: "Minute"
	}
};});