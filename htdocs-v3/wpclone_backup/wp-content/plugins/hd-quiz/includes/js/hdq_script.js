console.log("HD Quiz Loaded");
const hdq_locals = JSON.parse(hdq_local_vars);
const HDQ = {
	EL: {
		quizzes: document.getElementsByClassName("hdq_quiz"),
		results: document.getElementsByClassName("hdq_results_wrapper")[0],
		questions: document.getElementsByClassName("hdq_question"),
		next: document.getElementsByClassName("hdq_next"),
		finish: document.getElementsByClassName("hdq_finsh_button"),
		answers: document.getElementsByClassName("hdq_option"),
		loading: document.getElementsByClassName("hdq_loading_bar")[0],
		jPaginate: document.getElementsByClassName("hdq_jPaginate_button")
	},
	VARS: {
		ajax: hdq_locals.hdq_ajax,
		featured_image: hdq_locals.hdq_featured_image,
		pass_percent: hdq_locals.hdq_pass_percent,
		id: hdq_locals.hdq_quiz_id,
		name: hdq_locals.hdq_quiz_name,
		permalink: hdq_locals.hdq_quiz_permalink,
		mark_correct: hdq_locals.hdq_results_correct,
		share_results: hdq_locals.hdq_share_results,
		show_extra_text: hdq_locals.hdq_show_extra_text,
		show_results: hdq_locals.hdq_show_results,
		show_results_now: hdq_locals.hdq_show_results_now,
		stop_reselect: hdq_locals.hdq_stop_answer_reselect,
		submit_actions: hdq_locals.hdq_submit,
		timer: { time: hdq_locals.hdq_timer, max: hdq_locals.hdq_timer, question: hdq_locals.hdq_timer_question, active: false },
		twitter: hdq_locals.hdq_twitter_handle,
		ads: hdq_locals.hdq_use_ads,
		hdq_score: [],
		jPage: 0,
		paginate: false
	},
	init: function () {
		console.log("HD Quiz Init");
		if (HDQ.EL.quizzes.length > 1) {
			for (let i = 0; i < HDQ.EL.quizzes.length; i++) {
				let html = `<p>HD QUIZ - WARNING: There is more than one quiz on this page. Due to the complexity of HD Quiz, only one quiz should on a page at a time.</p>`;
				HDQ.EL.quizzes[i].insertAdjacentHTML("beforebegin", html);
			}
		}

		if (HDQ.VARS.timer.question === "yes") {
			HDQ.VARS.stop_reselect = "yes";
		}

		// when an answer has been made
		for (let i = 0; i < HDQ.EL.answers.length; i++) {
			HDQ.EL.answers[i].addEventListener("change", HDQ.validate.check);
		}

		// when finish button selected
		for (let i = 0; i < HDQ.EL.finish.length; i++) {
			HDQ.EL.finish[i].addEventListener("click", HDQ.submit);
		}

		// jPaginate
		for (let i = 0; i < HDQ.EL.jPaginate.length; i++) {
			HDQ.EL.jPaginate[i].addEventListener("click", HDQ.jPaginate);
		}

		// WP Pagination
		const nextButton = document.getElementsByClassName("hdq_next_page_button");
		for (let i = 0; i < nextButton.length; i++) {
			nextButton[i].addEventListener("click", function (e) {
				if (!HDQ.VARS.paginate) {
					e.preventDefault();
					HDQ.paginate(this);
				}
			});
		}

		if (HDQ.VARS.timer.max > 3) {
			if (HDQ.VARS.ads !== true) {
				const start = document.getElementsByClassName("hdq_quiz_start");
				for (let i = 0; i < start.length; i++) {
					start[i].addEventListener("click", HDQ.timer.init);
				}
			} else {
				HDQ.timer.init();
			}
		}
	},
	timer: {
		init: function () {
			try {
				this.remove();
			} catch (e) {}
			let quizzes = document.getElementsByClassName("hdq_quiz");
			for (let i = 0; i < quizzes.length; i++) {
				quizzes[i].style.display = "block";
			}

			const html = `<div class = "hdq_timer"></div>`;
			document.body.insertAdjacentHTML("beforeend", html);

			HDQ.VARS.timer.active = true;
			if (HDQ.VARS.timer.question === "yes") {
				HDQ.timer.question.init();
			} else {
				HDQ.timer.quiz();
			}
		},
		quiz: function () {
			if (HDQ.VARS.timer.time > 0 && HDQ.VARS.timer.active == true) {
				let minutes = parseInt(HDQ.VARS.timer.time / 60);
				minutes = minutes < 10 ? "0" + minutes : minutes;
				let seconds = HDQ.VARS.timer.time % 60;
				seconds = seconds < 10 ? "0" + seconds : seconds;
				let t = minutes + ":" + seconds;
				jQuery(".hdq_timer").html(t);
				if (HDQ.VARS.timer.time > 10 && HDQ.VARS.timer.time < 30) {
					jQuery(".hdq_timer").addClass("hdq_timer_warning");
				} else if (HDQ.VARS.timer.time <= 10) {
					jQuery(".hdq_timer").removeClass("hdq_timer_warning");
					jQuery(".hdq_timer").addClass("hdq_timer_danger");
				}
				HDQ.VARS.timer.time = HDQ.VARS.timer.time - 1;
				setTimeout(HDQ.timer.quiz, 1000);
			} else {
				if (HDQ.VARS.timer.active == true) {
					// uh oh! Out of time
					jQuery(".hdq_timer").html("0");
					jQuery(".hdq_timer").removeClass("hdq_timer_danger");
					jQuery(".hdq_finsh_button").click(); // submit quiz for completion
					HDQ.VARS.timer.active = false;
				} else {
					// user finished in time
					jQuery(".hdq_timer").removeClass("hdq_timer_danger");
					jQuery(".hdq_timer").removeClass("hdq_timer_warning");
					jQuery(".hdq_timer").removeClass("hdq_timer_danger");
					jQuery(".hdq_timer").removeClass("hdq_timer_warning");
				}
			}
		},
		question: {
			init: async function () {
				for (let i = 0; i < HDQ.EL.answers.length; i++) {
					HDQ.EL.answers[i].disabled = true;
					HDQ.EL.answers[i].addEventListener("change", HDQ.timer.question.changed);
					let p = await HDQ.getParent(HDQ.EL.answers[i]);
					p.classList.add("hdq_disabled");
				}
				// reenable the first question answers
				let parent = await HDQ.getParent(HDQ.EL.answers[0]);
				parent.classList.add("hdq_active_question");
				let answers = parent.getElementsByClassName("hdq_option");
				p = await HDQ.getParent(answers[0]);
				p.classList.remove("hdq_disabled");

				for (let i = 0; i < answers.length; i++) {
					answers[i].disabled = false;
				}
				HDQ.timer.question.question();

				// hide jPagination until answer has been made
				for (let i = 0; i < HDQ.EL.jPaginate.length; i++) {
					HDQ.EL.jPaginate[i].style.display = "none";
				}
			},
			checkQuestion: async function (el) {
				if (el.classList.contains("hdq_question")) {
					let answers = el.getElementsByClassName("hdq_option");
					if (answers.length > 0) {
						let p = await HDQ.getParent(answers[0]);
						p.classList.remove("hdq_disabled");
						for (let i = 0; i < answers.length; i++) {
							answers[i].disabled = false;
						}
						el.classList.add("hdq_active_question");
						return "success";
					} else {
						// probably a question as title
						let next_question = el.nextSibling;
						return await HDQ.timer.question.checkQuestion(next_question);
					}
				} else {
					if (el.classList.contains("hdq_jPaginate")) {
						el.style.display = "block";
						el.firstChild.style.display = "block";
						el.firstChild.click();
						let next_question = el.nextSibling;
						return await HDQ.timer.question.checkQuestion(next_question);
					} else {
						return "complete";
					}
				}
			},
			changed: async function () {
				// reset timer
				HDQ.VARS.timer.time = HDQ.VARS.timer.max;

				// figure out what the next question is
				let p = await HDQ.getParent(this);
				let next_question = p.nextSibling;
				let active_question = document.getElementsByClassName("hdq_active_question");
				if (active_question.length > 0) {
					active_question[0].classList.remove("hdq_active_question");
				}
				let status = await HDQ.timer.question.checkQuestion(next_question);

				if (status === "complete") {
					// end the quiz
					HDQ.VARS.timer.active = false;
					jQuery(".hdq_finsh_button").click(); // submit quiz for completion
				} else {
					jQuery(".hdq_timer").removeClass("hdq_timer_danger");
					jQuery(".hdq_timer").removeClass("hdq_timer_warning");
				}
			},
			question: async function (isFirst = false) {
				if (HDQ.VARS.timer.time > 0 && HDQ.VARS.timer.active == true) {
					let minutes = parseInt(HDQ.VARS.timer.time / 60);
					minutes = minutes < 10 ? "0" + minutes : minutes;
					let seconds = HDQ.VARS.timer.time % 60;
					seconds = seconds < 10 ? "0" + seconds : seconds;
					let t = minutes + ":" + seconds;
					jQuery(".hdq_timer").html(t);
					if (HDQ.VARS.timer.time > 10 && HDQ.VARS.timer.time < 30) {
						jQuery(".hdq_timer").addClass("hdq_timer_warning");
					} else if (HDQ.VARS.timer.time <= 10) {
						jQuery(".hdq_timer").removeClass("hdq_timer_warning");
						jQuery(".hdq_timer").addClass("hdq_timer_danger");
					}
					HDQ.VARS.timer.time = HDQ.VARS.timer.time - 1;
					setTimeout(HDQ.timer.question.question, 1000);
				} else {
					if (HDQ.VARS.timer.active == true) {
						let active_question = document.getElementsByClassName("hdq_active_question");
						if (active_question.length > 0) {
							active_question = active_question[0];
							let answers = active_question.getElementsByClassName("hdq_option");
							for (let i = 0; i < answers.length; i++) {
								answers[i].disabled = true;
							}

							// figure out what the next question is
							let next_question = active_question.nextSibling;
							active_question.classList.remove("hdq_active_question");
							let status = await HDQ.timer.question.checkQuestion(next_question);

							if (status === "complete") {
								// end the quiz
								jQuery(".hdq_finsh_button").click(); // submit quiz for completion
							} else {
								// reset timer
								jQuery(".hdq_timer").html("0");
								HDQ.VARS.timer.time = HDQ.VARS.timer.max;

								jQuery(".hdq_timer").removeClass("hdq_timer_danger");
								jQuery(".hdq_timer").removeClass("hdq_timer_warning");

								setTimeout(HDQ.timer.question.question, 1000);
							}
						} else {
							console.warn("there is no active question?");
						}
					} else {
						console.log("quiz has completed");
					}
				}
			}
		}
	},
	validate: {
		all: async function () {
			for (let i = 0; i < HDQ.EL.answers.length; i++) {
				let t = await HDQ.validate.type(HDQ.EL.answers[i]);
				if (t != "") {
					let result = await HDQ.validate[t](HDQ.EL.answers[i]);
				}
			}
		},
		check: async function () {
			let t = await HDQ.validate.type(this);
			if (t != "") {
				if (HDQ.VARS.show_results_now === "yes" && HDQ.VARS.show_results === "yes") {
					let result = await HDQ.validate[t](this);
					HDQ.validate.extraText(result, this);
				}

				if (HDQ.VARS.stop_reselect === "yes") {
					await HDQ.validate.disable(this);
				} else {
					await HDQ.validate.checkToRadio(this);
				}
			}
		},
		type: async function (el) {
			let t = el.getAttribute("data-type");
			if (t) {
				return t;
			} else {
				return "";
			}
		},
		checkToRadio(el) {
			let question = el.getAttribute("data-id");
			question = document.getElementById("hdq_question_" + question);
			let answers = question.querySelectorAll(".hdq_option");
			for (let i = 0; i < answers.length; i++) {
				if (answers[i] != el) {
					answers[i].checked = false;
				}
			}
		},
		disable: async function (el) {
			let question = el.getAttribute("data-id");
			question = document.getElementById("hdq_question_" + question);
			let answers = question.querySelectorAll(".hdq_option");
			for (let i = 0; i < answers.length; i++) {
				answers[i].disabled = true;
			}
		},
		extraText: async function (result, el) {
			if (HDQ.VARS.show_extra_text == "yes") {
				await showExtraText(el);
			} else {
				if (!result) {
					await showExtraText(el);
				}
			}

			async function showExtraText(el) {
				let question = el.getAttribute("data-id");
				question = document.getElementById("hdq_question_" + question);
				let extra_text = question.querySelector(".hdq_question_after_text");
				if (extra_text != null) {
					extra_text.style.display = "block";
				}
			}
		},
		text: async function (el) {
			let correct = false;
			let value = el.value.toUpperCase();
			let answers = el.getAttribute("data-answers");
			answers = decodeURIComponent(answers);
			answers = JSON.parse(answers);
			if (answers.includes(value)) {
				if (HDQ.VARS.show_results == "yes") {
					el.parentNode.classList.add("hdq_correct");
				}
				correct = true;
			} else {
				if (HDQ.VARS.show_results == "yes") {
					el.parentNode.classList.add("hdq_wrong");
				}
				if (HDQ.VARS.mark_correct === "yes") {
					if (!el.parentNode.classList.contains("hdq_answered")) {
						let data = " - [" + answers[0] + "]";
						el.value = el.value + data;
						el.parentNode.classList.add("hdq_answered");
					}
				}
			}
			el.disabled = true;
			return correct;
		},
		radio: async function (el) {
			let correct = false;
			let row = el.parentNode.parentNode.parentNode;
			if (el.value == 1) {
				if (el.checked == true) {
					correct = true;
					if (HDQ.VARS.show_results == "yes") {
						row.classList.add("hdq_correct");
					}
				}
			} else {
				if (el.checked == true) {
					if (HDQ.VARS.show_results == "yes") {
						row.classList.add("hdq_wrong");
					}
				}
			}

			let question = el.getAttribute("data-id");
			question = document.getElementById("hdq_question_" + question);
			let answers = question.querySelectorAll(".hdq_option");
			for (let i = 0; i < answers.length; i++) {
				if (HDQ.VARS.mark_correct === "yes") {
					if (answers[i].value == 1) {
						row = answers[i].parentNode.parentNode.parentNode;
						row.classList.add("hdq_correct_not_selected");
					}
				}
				answers[i].disabled = true;
			}
			return correct;
		},
		image: async function (el) {
			let correct = false;
			let row = el.parentNode.parentNode.parentNode.parentNode;
			if (el.value == 1) {
				if (el.checked == true) {
					correct = true;
					if (HDQ.VARS.show_results == "yes") {
						row.classList.add("hdq_correct");
					}
				}
			} else {
				if (el.checked == true) {
					if (HDQ.VARS.show_results == "yes") {
						row.classList.add("hdq_wrong");
					}
				}
			}

			let question = el.getAttribute("data-id");
			question = document.getElementById("hdq_question_" + question);
			let answers = question.querySelectorAll(".hdq_option");
			for (let i = 0; i < answers.length; i++) {
				if (HDQ.VARS.mark_correct === "yes") {
					if (answers[i].value == 1) {
						row = answers[i].parentNode.parentNode.parentNode.parentNode;
						row.classList.add("hdq_correct_not_selected");
					}
				}
				answers[i].disabled = true;
			}
			return correct;
		}
	},
	getResult: {
		text_based: async function (answers) {
			let result = await HDQ.validate.text(answers[0]);
			HDQ.validate.extraText(result, answers[0]);
			return result;
		},
		multiple_choice_text: async function (answers) {
			for (let i = 0; i < answers.length; i++) {
				if (answers[i].checked == true) {
					let result = await HDQ.validate.radio(answers[i]);
					if (result) {
						HDQ.validate.extraText(true, answers[0]);
						return 1;
					}
				}
			}
			HDQ.validate.extraText(false, answers[0]);
			return 0;
		},
		multiple_choice_image: async function (answers) {
			for (let i = 0; i < answers.length; i++) {
				if (answers[i].checked == true) {
					let result = await HDQ.validate.image(answers[i]);
					if (result) {
						HDQ.validate.extraText(true, answers[0]);
						return 1;
					}
				}
			}
			HDQ.validate.extraText(false, answers[0]);
			return 0;
		}
	},
	calculateScore: async function () {
		let total_score = 0;
		let total_questions = 0;
		let cs = document.getElementById("hdq_current_score");
		let tq = document.getElementById("hdq_total_questions");
		if (cs != null && tq != null) {
			total_score = parseInt(cs.value);
			total_questions = parseInt(tq.value);
		}
		total_questions += parseInt(HDQ.EL.questions.length);

		for (let i = 0; i < HDQ.EL.questions.length; i++) {
			let t = HDQ.EL.questions[i].getAttribute("data-type");
			let answers = HDQ.EL.questions[i].querySelectorAll(".hdq_option");
			if (answers.length > 0) {
				total_score += await HDQ.getResult[t](answers);
			} else {
				total_questions -= 1;
			}
		}

		HDQ.VARS.hdq_score = [parseInt(total_score), parseInt(total_questions)];
		return HDQ.VARS.hdq_score;
	},
	submit: async function () {
		if (this.classList.contains("hdq_complete")) {
			return;
		}

		HDQ.VARS.timer.active = false;

		// start visual feedback
		let quiz_ID = this.getAttribute("data-id");
		this.innerHTML = "...";
		this.classList.add("hdq_complete");
		jQuery(this).fadeOut("slow");
		HDQ.EL.loading.classList.add("hdq_animate");

		// show all questions in case of jPagination
		jQuery(".hdq_question").fadeIn();

		// validate all answers
		await HDQ.validate.all();
		// figure out the score
		let score = await HDQ.calculateScore();
		let data = score[0] + " / " + score[1];

		// update results section
		if (jQuery(".hdq_results_inner .hdq_result .hdq_result_percent")[0]) {
			let hdq_results_percent = (parseFloat(HDQ.VARS.hdq_score[0]) / parseFloat(HDQ.VARS.hdq_score[1])) * 100;
			hdq_results_percent = Math.ceil(hdq_results_percent);
			data =
				'<span class = "hdq_result_fraction">' +
				data +
				'</span> - <span class = "hdq_result_percent">' +
				hdq_results_percent +
				"%</span>";
		}
		jQuery(".hdq_results_inner .hdq_result").html(data);

		let pass_percent = 0;
		pass_percent = score[0] / score[1];
		pass_percent = pass_percent * 100;
		if (pass_percent >= HDQ.VARS.pass_percent) {
			jQuery(".hdq_result_pass").show();
		} else {
			jQuery(".hdq_result_fail").show();
		}

		if (HDQ.VARS.share_results === "yes") {
			HDQ.share();
		}
		jQuery(".hdq_results_wrapper").fadeIn();

		// Action onSubmit Quiz: TODO: Async Await this for better compatibility
		if (typeof HDQ.VARS.submit_actions != undefined && HDQ.VARS.submit_actions != null) {
			for (let i = 0; i < HDQ.VARS.submit_actions.length; i++) {
				HDQ.submitAction(HDQ.VARS.submit_actions[i]);
			}
		}

		HDQ.scroll();
	},
	submitAction: function (action) {
		console.log("onSumbit action: " + action);
		let data = {};
		// if this is also a JS function, store data
		if (typeof window[action] !== "undefined") {
			let extra = window[action]();
			if (extra) {
				data.extra = window[action]();
			}
		}

		// small delay since this isn't syncronous
		setTimeout(function () {
			data.quizID = HDQ.VARS.id;
			data.score = HDQ.VARS.hdq_score;
			// send data to admin-ajax
			console.log(data);
			console.log(action);
			jQuery.ajax({
				type: "POST",
				data: {
					action: action,
					data: data
				},
				url: HDQ.VARS.ajax,
				success: function (res) {
					console.log(res);
				}
			});
		}, 100);
	},
	share: function () {
		function create_twitter_share() {
			let baseURL = "https://twitter.com/intent/tweet?screen_name=";
			let shareText = HDQ.VARS.hdq_score[0] + "/" + HDQ.VARS.hdq_score[1] + " on the " + HDQ.VARS.name + " quiz. Can you beat me? ";
			shareText = encodeURI(shareText);
			let shareLink = baseURL + HDQ.VARS.twitter + "&text=" + shareText + encodeURI(HDQ.VARS.permalink);
			jQuery(".hdq_twitter").attr("href", shareLink);
		}
		create_twitter_share();
	},
	jPaginate: function () {
		let hdq_form_id = jQuery(this).attr("data-id");
		jQuery(".hdq_jPaginate .hdq_next_button").removeClass("hdq_next_selected");
		jQuery(this).addClass("hdq_next_selected");

		jQuery("#hdq_" + hdq_form_id + " .hdq_jPaginate:visible")
			.prevAll("#hdq_" + hdq_form_id + " .hdq_question")
			.hide();
		jQuery("#hdq_" + hdq_form_id + " .hdq_jPaginate:eq(" + parseInt(HDQ.VARS.jPage) + ")")
			.nextUntil("#hdq_" + hdq_form_id + " .hdq_jPaginate ")
			.show();
		jQuery(".hdq_results_wrapper").hide(); // in case the results are below the quiz
		HDQ.VARS.jPage = parseInt(HDQ.VARS.jPage + 1);

		if (HDQ.VARS.jPage === HDQ.EL.jPaginate.length) {
			jQuery(".hdq_finsh_button").removeClass("hdq_hidden");
		}

		jQuery(this).parent().hide();

		jQuery("#hdq_" + hdq_form_id + " .hdq_jPaginate:eq(" + parseInt(HDQ.VARS.jPage) + ")").show();
		setTimeout(async function () {
			let hdq_quiz_container = document.querySelector("#hdq_" + hdq_form_id);
			hdq_quiz_container = jQuery(await HDQ.get_quiz_parent_container(hdq_quiz_container));

			if (hdq_quiz_container[0].tagName === "DIV") {
				hdq_top =
					jQuery(hdq_quiz_container).scrollTop() +
					jQuery(".hdq_question:visible").offset().top -
					jQuery(".hdq_question:visible").height() / 2 -
					40;
				jQuery(hdq_quiz_container).animate(
					{
						scrollTop: hdq_top
					},
					550
				);
			} else {
				let overflowH = jQuery("html").css("overflow");
				let overflowB = jQuery("body").css("overflow");
				let rest = false;
				if (overflowH.indexOf("hidden") >= 0 || overflowB.indexOf("hidden") >= 0) {
					rest = true;
				}

				jQuery("html,body").css("overflow", "initial");

				jQuery("html,body").animate(
					{
						scrollTop: jQuery(".hdq_question:visible").offset().top - 40
					},
					550
				);

				if (rest) {
					setTimeout(function () {
						jQuery("html").css("overflow", overflowH);
						jQuery("body").css("overflow", overflowB);
					}, 550);
				}
			}
		}, 50);
	},
	paginate: async function (el) {
		// get load values
		let score = await HDQ.calculateScore();
		console.log(score);
		let href = el.getAttribute("href");
		href = href + score[0] + "&totalQuestions=" + score[1];
		el.setAttribute("href", href);
		HDQ.VARS.paginate = true;
		el.click();
	},
	get_quiz_parent_container: async function (element, includeHidden) {
		var style = getComputedStyle(element);
		var excludeStaticParent = style.position === "absolute";
		var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

		if (style.position === "fixed") return document.body;
		for (var parent = element; (parent = parent.parentElement); ) {
			style = getComputedStyle(parent);
			if (excludeStaticParent && style.position === "static") {
				continue;
			}
			if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
		}
		return document.body;
	},
	scroll: function () {
		// this is super not accurate, but covers most themes.
		setTimeout(async function () {
			let hdq_quiz_container = document.querySelector("#hdq_" + HDQ.VARS.id);
			hdq_quiz_container = jQuery(await HDQ.get_quiz_parent_container(hdq_quiz_container));
			console.log("container:");
			console.log(hdq_quiz_container);

			if (hdq_quiz_container[0].tagName === "DIV") {
				hdq_top =
					jQuery(hdq_quiz_container).scrollTop() +
					jQuery(".hdq_results_wrapper").offset().top -
					jQuery(".hdq_results_wrapper").height() / 2 -
					100;
				console.log("hdq_top: " + hdq_top);
				jQuery(hdq_quiz_container).animate(
					{
						scrollTop: hdq_top
					},
					550
				);
				jQuery("html,body").animate(
					{
						scrollTop: hdq_top
					},
					550
				);
			} else {
				let overflowH = jQuery("html").css("overflow");
				let overflowB = jQuery("body").css("overflow");
				let rest = false;
				if (overflowH.indexOf("hidden") >= 0 || overflowB.indexOf("hidden") >= 0) {
					rest = true;
				}

				jQuery("html,body").css("overflow", "initial");

				jQuery("html,body").animate(
					{
						scrollTop: jQuery(".hdq_results_wrapper").offset().top - 100
					},
					550
				);

				if (rest) {
					setTimeout(function () {
						jQuery("html").css("overflow", overflowH);
						jQuery("body").css("overflow", overflowB);
					}, 550);
				}
			}
		}, 50);
	},
	// gets find the parent question of an element
	getParent: async function (el) {
		let p = el.parentNode;
		if (p.classList.contains("hdq_question")) {
			return p;
		} else {
			p = HDQ.getParent(p);
		}
		return p;
	}
};
HDQ.init();

/* FB APP - Only used if APP ID was provided */
jQuery("#hdq_fb_sharer").click(function () {
	let hdq_score = jQuery(".hdq_result").text();
	/*
        * Facebook no longer allows us to send custom share images :(
        let hdq_share_image = jQuery(".hdq_result_" + hdq_pass_or_fail)
            .find("img")
            .attr("src");
        if (hdq_share_image != "" && hdq_share_image != null) {
            // for things like jetpack proton images
            if (hdq_share_image.startsWith("//")) {
                console.log("image starts with // : fixing");
                hdq_share_image = "https:" + hdq_share_image;
            }
        } else {
            // no images in success or fail area
            hdq_share_image = hdq_featured_image;
        }
	*/

	FB.ui(
		{
			method: "share",
			href: HDQ.VARS.permalink,
			hashtag: "#hdquiz",
			quote: "I scored " + hdq_score + " on the " + HDQ.VARS.name + " quiz. Can you beat me?"
		},
		function (res) {}
	);
});
