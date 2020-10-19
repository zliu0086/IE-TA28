const HDQ = {
	EL: {
		upload: document.getElementById("hdq_csv_file_upload"),
		begin: document.getElementById("hdq_start_csv_upload"),
		log: document.getElementById("hdq_message_logs"),
	},
	VARS: {
		file: null,
		nonce: document.getElementById("hdq_tools_nonce").value,
	},
	init: function () {
		console.log("HDQ: Importer loaded");
		if (HDQ.EL.upload != null) {
			HDQ.EL.upload.addEventListener("change", function (e) {
				HDQ.VARS.file = this.files[0];
				let filename = HDQ.EL.upload.value.split("\\").pop();
				jQuery(".hdq_file_label").html(filename);
			});
		}
	},
	uploadCSV: function () {
		let upload = new Upload(HDQ.VARS.file);
		upload.doUpload();
	},
	parseNext: function (path, current, total) {
		let p = path;
		let c = parseInt(current);
		let t = parseInt(total);

		jQuery.ajax({
			type: "POST",
			data: {
				action: "hdq_parse_csv_data",
				nonce: HDQ.VARS.nonce,
				start: current,
				path: path,
			},
			url: ajaxurl,
			success: function (data) {
				console.log(data);
				if (c >= total) {
					c = total;
				}
				if (c != total) {
					let item = `<div class = "hdq_log_item">added ${c} / ${total} questions</div>`;
					HDQ.EL.log.insertAdjacentHTML("afterbegin", item);
					setTimeout(function () {
						HDQ.parseNext(p, c + 1, t);
					}, 2000); // delay to stop overloading slow servers
				} else {
					let item = `<div class = "hdq_log_item" style = "color:darkseagreen">ALL QUESTIONS HAVE BEEN ADDED</div>`;
					HDQ.EL.log.insertAdjacentHTML("afterbegin", item);
				}
			},
			error: function () {
				let item = `<div class = "hdq_log_item" style = "color:darkred">THERE WAS A SERVER ERROR ADDING ONE OF YOUR QUIZZES</div>`;
				HDQ.EL.log.insertAdjacentHTML("afterbegin", item);
			},
		});
	},
};
HDQ.init();
