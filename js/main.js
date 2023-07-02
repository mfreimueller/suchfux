var baseUrl = "https://michaeleph.at/suchfux";

$(document).ready(function () {
	$("#nextQuery").hide();

	var session = getSession();
	if (!session) {
		session = {
			query: {},
			attempts: 0,
			maxAttempts: 3,
			answers: [],
			round: 0,
			score: 0
		};
		writeSession(session);

		fetchQuery();
	} else {
		$("#query").val(session.query.query + " ... ");
		$("#max-attempts").val(session.maxAttempts);

		// display all already given answers
		for (var idx = 0; idx < session.answers.length; idx++) {
			var sidx = session.answers[idx];
			var suggestion = session.query.suggestions[sidx];
			$('*[data-target="result-' + sidx + '"]').text(suggestion);
		}

		if (attemptsExhausted()) {
			finalizeBoard();
		}

		updateStatistics();
	}
});

function getSession() {
	return JSON.parse(localStorage.getItem("session"));
}

function writeSession(session) {
	localStorage.setItem("session", JSON.stringify(session));
}

function fetchQuery() {
	$.get(baseUrl + "/randomQuery.php", function (data) {
		$("#query").val(data.query);

		var session = getSession();
		session.round++;
		session.query = data;
		writeSession(session);

		$("#query").val(session.query.query + " ... ");
	});
}

function updateStatistics() {
	var session = getSession();

	$("#roundCounter").text(session.round);
	$("#score").text(session.score);
	$("#attemptCounter").text(session.attempts + 1);
	$("#maxAttempts").text(session.maxAttempts);
}

function finalizeBoard() {
	var session = getSession();

	for (var idx = 0; idx < 10; idx++) {
		if (!session.answers.includes(idx)) {
			var suggestion = session.query.suggestions[idx];
			$('*[data-target="result-' + idx + '"]').text(suggestion);
			$('*[data-target="result-' + idx + '"]').addClass("light");
		}
	}

	$("#newQuery").hide();
	$("#nextQuery").show();
}

/**
 * Removes all result texts and resets the counter.
 */
function clearBoard() {
	var session = getSession();
	session.attempts = 0;
	session.answers = [];

	writeSession(session);
	updateStatistics();

	$("#suggestion").val("");

	for (var idx = 0; idx < 10; idx++) {
		$('*[data-target="result-' + idx + '"]').text("");

		$('*[data-target="result-' + idx + '"]').removeClass("light");
	}

	$("#newQuery").show();
	$("#nextQuery").hide();
}

function attemptsExhausted() {
	var session = getSession();
	return session.attempts >= session.maxAttempts;
}

$("#saveSettings").click(function () {
	var session = getSession();
	var newMaxAttempts = +($("#max-attempts").val());

	session.maxAttempts = newMaxAttempts;
	writeSession(session);

	// we reset the current game and fetch a new query
	clearBoard();
	fetchQuery();
});

$(".restart").click(function () {
	clearBoard();
	fetchQuery();
});

$("#suggestion").on("keydown", function (event) {
	// keyCode 13 is enter key
	if (event.which == 13 && !attemptsExhausted()) {
		var suggestion = $(this).val();

		var session = getSession();
		var correctAnswer = false;

		var searchTerms = [
			session.query.query + suggestion,
			session.query.query + " " + suggestion
		];

		for (var idxST = 0; idxST < searchTerms.length; idxST++) {
			var searchTerm = searchTerms[idxST];
			var alreadyIncluded = false;

			for (var idx = 0; idx < session.query.suggestions.length; idx++) {
				var correct = session.query.suggestions[idx];
				if (correct.toLowerCase() === searchTerm.toLowerCase()) {
					// it is the correct suggestion.
					// if the user already prompted the answer before, we increase his counter
					if (session.answers.includes(idx)) {
						alreadyIncluded = true;
					} else {
						correctAnswer = true;

						session.answers.push(idx);
						session.score += (100 - (idx * 10));

						$('*[data-target="result-' + idx + '"]').text(correct);
					}

					break;
				}
			}

			if (alreadyIncluded) {
				session.attempts++;
				break;
			}
		}

		$("#suggestion").val("");

		if (correctAnswer) {
			writeSession(session);

			// has the user given all suggestions?
			if (session.answers === session.query.suggestions.length) {
				finalizeBoard();
			} else {
				updateStatistics();
			}
		} else {
			session.attempts++;
			writeSession(session);

			if (attemptsExhausted()) {
				finalizeBoard();
			} else {
				updateStatistics();
			}
		}
	}
});