/*

@license
dhtmlxScheduler v.5.3.10 Professional

This software is covered by DHTMLX Commercial License. Usage without proper license is prohibited.

(c) XB Software Ltd.

*/
Scheduler.plugin(function(scheduler){/*

 TRANSLATION BY MATTHEUS PIROVANI RORIZ GONЗALVES

 mattheusroriz@hotmail.com / mattheus.pirovani@gmail.com /

 www.atrixian.com.br

 */

scheduler.locale = {
	date: {
		month_full: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
		month_short: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
		day_full: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"],
		day_short: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
	},
	labels: {
		dhx_cal_today_button: "Hoje",
		day_tab: "Dia",
		week_tab: "Semana",
		month_tab: "Mês",
		new_event: "Novo evento",
		icon_save: "Salvar",
		icon_cancel: "Cancelar",
		icon_details: "Detalhes",
		icon_edit: "Editar",
		icon_delete: "Deletar",
		confirm_closing: "", //Your changes will be lost, are your sure ?
		confirm_deleting: "Tem certeza que deseja excluir?",
		section_description: "Descrição",
		section_time: "Período de tempo",
		full_day: "Dia inteiro",

		confirm_recurring: "Deseja editar todos esses eventos repetidos?",
		section_recurring: "Repetir evento",
		button_recurring: "Desabilitar",
		button_recurring_open: "Habilitar",
		button_edit_series: "Editar a série",
		button_edit_occurrence: "Editar uma cópia",

		/*agenda view extension*/
		agenda_tab: "Dia",
		date: "Data",
		description: "Descrição",

		/*year view extension*/
		year_tab: "Ano",

		/*week agenda view extension*/
		week_agenda_tab: "Dia",

		/*grid view extension*/
		grid_tab: "Grade",

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
		minute: "Minute",

		/* recurring event components */
		repeat_radio_day: "Diário",
		repeat_radio_week: "Semanal",
		repeat_radio_month: "Mensal",
		repeat_radio_year: "Anual",
		repeat_radio_day_type: "Cada",
		repeat_text_day_count: "dia(s)",
		repeat_radio_day_type2: "Cada trabalho diário",
		repeat_week: " Repita cada",
		repeat_text_week_count: "semana:",
		repeat_radio_month_type: "Repetir",
		repeat_radio_month_start: "Em",
		repeat_text_month_day: "todo dia",
		repeat_text_month_count: "mês",
		repeat_text_month_count2_before: "todo",
		repeat_text_month_count2_after: "mês",
		repeat_year_label: "Em",
		select_year_day2: "of",
		repeat_text_year_day: "dia",
		select_year_month: "mês",
		repeat_radio_end: "Sem data final",
		repeat_text_occurences_count: "ocorrências",
		repeat_radio_end3: "Fim",
		repeat_radio_end2: "Depois",
		month_for_recurring: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
		day_for_recurring: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"]
	}
};


});