// script.js

var IS_DEBUG;

// デバッグ時は下記を trun にしてください
//IS_DEBUG = true;

var sex_name, sex_label;

jQuery(document).ready(function($){
  // ImageUpload ボタンの初期化
  initializeImageUpload();

  if (IS_DEBUG) {
    $("#debug_view").show();
  }
});

function initializeImageUpload() {
	// Initialization of input elements and ImageUploader.js
	$("input.image-upload").each(function(index){
		var id_product = $(this).attr('data-product');
		var uploader = new ImageUploader({'inputElement': $(this).get(0),
			'onComplete': function() {
				// Hide progress bar
        $("#upload-progress").hide();

        // 結果送信ボタンを有効にする
        $("#send_button").removeAttr('disabled');
			},
			//'debug': true
			});
	});

	// The function below is triggered every time the user selects a file
	$("input.image-upload").change(function(index){
		// We will check additionally the extension of the image if it's correct and we support it
		var extension = $(this).val();
		if (extension.length > 0) {
		  extension = extension.match(/[^.]+$/).pop().toLowerCase();
			extension = ~$.inArray(extension, ['jpg', 'jpeg']);
		} else {
			event.preventDefault();
			return;
    }

		if (!extension) {
			event.preventDefault();
			console.error('Unsupported image format');
			return;
		}
		// Show progress bar
    $("#upload-progress").show();
	});
}

// 男子・女子・シニアそれぞれの試合パターンを返す
function getMatchPattern() {
  var matchPatterns = {
    man : [ "S1", "S2", "D1", "D2", "D3" ],
    woman : [ "S1", "D1", "D2" ],
    senior : [ "D1", "D2", "D3" ]
  };

  if (document.getElementById("radio_man").checked) {
    return matchPatterns.man;
  }
  if (document.getElementById("radio_woman").checked) {
    return matchPatterns.woman;
  }
  if (document.getElementById("radio_senior").checked) {
    return matchPatterns.senior;
  }

  // デフォルトは男子パターンにしておく
  return matchPatterns.man;
}

function generateMatchLeagueOption(matchLeagues) {
  // 既存の子ノードを削除
  $("#match_league").empty();

  // tournament-info.js で定義された leagueInfo に従い、select/optoin を生成していく
  matchLeagues.forEach(function(matchLeague) {
    var optgroup = document.createElement("optgroup");
    optgroup.label = matchLeague.label;
    matchLeague.leagues.forEach(function(league) {
      var option = document.createElement("option");
      option.value = league.name;
      option.text = league.text;
      optgroup.appendChild(option);
    });
    $("#match_league").append(optgroup);
  });
}

// 男子・女子・シニアを選択したときの処理
function onSexUpdate() {
  sex_name = sex_label = "";
  if (document.getElementById("radio_man").checked) {
    sex_name = "man";
    sex_label = "男子";
    generateMatchLeagueOption(leagueInfo.man);
  }
  if (document.getElementById("radio_woman").checked) {
    sex_name = "woman";
    sex_label = "女子";
    generateMatchLeagueOption(leagueInfo.woman);
  }
  if (document.getElementById("radio_senior").checked) {
    sex_name = "senior";
    sex_label = "シニア";
    generateMatchLeagueOption(leagueInfo.senior);
  }

  // 男子・女子・シニアの選択に応じて、入力すべきフィールドだけにする
  var matchPattern = getMatchPattern();
  [ "S1", "S2", "D1", "D2", "D3" ].forEach(function(match) {
    if (matchPattern.indexOf(match) != -1) {
      $("#game_" + match).show();
    } else {
      $("#game_" + match).hide();
    }
  });

  // チーム選択フィールドの設定
	$(".team-selector").each(function(index, element) {
    var teams = teamInfo[sex_name];

    // 既存の子ノードを削除
    $(this).empty();
    // チーム選択フィールドを設定
    teams.forEach(function(team) {
      var option = document.createElement("option");
      option.text = team;
      element.appendChild(option);
    });
  });
}

// 入力情報がアップデートされたときの処理
function onTableUpdate() {
  // match_name の設定
  var match_name = "", mail_subject = "";
  var match_league = $("#match_league option:selected");
  var match_number = $("#match_number option:selected");
  var team_L = $("#team_L option:selected");
  var team_R = $("#team_R option:selected");
  if (sex_name && match_league.val() != "" && match_number.val() != "") {
    match_name = sex_name + "_" + match_league.val() + "_No" + ("00" + match_number.val()).substr(-2);
    mail_subject = sex_label + "/" + match_league.text() + "/ 試合No" + match_number.val() + " (" + team_L.text() + " vs " + team_R.text() + ")";

    // 入力フィールドを表示させる
    $(".input-field").each(function(index){
      $(this).show();
    });
  } else {
    // 入力フィールドを非表示にする
    $(".input-field").each(function(index){
      $(this).hide();
    });
  }
  $("#mail_subject").val(mail_subject);
  $("#match_name").val(match_name);

  // 取得ゲーム数を計算する
  var matchPattern = getMatchPattern();
  var L_total = 0, R_total = 0;
  matchPattern.forEach(function(match) {
    L_total += parseInt($("#game_L_" + match).val());
    R_total += parseInt($("#game_R_" + match).val());
  });
  $("#game_L_total").text(L_total);
  $("#game_R_total").text(R_total);

  generateMailContents();
}

// メールに埋め込む部分の HTML を生成する
function generateMailContents() {
  // 上記のヒアドキュメント内のキーワードを置換する
  var data = {
    userAgent: navigator.userAgent,
    sex_label: sex_label,
    match_league: $("#match_league option:selected").text(),
    match_number: $("#match_number option:selected").text(),
    match_name: $("#match_name").val(),
    mail_subject: $("#mail_subject").val(),
    team_L: $("#team_L option:selected").text(),
    team_R: $("#team_R option:selected").text(),
    match_L: $("#match_L option:selected").text(),
    match_R: $("#match_R option:selected").text(),
    game_L_S1: $("#game_L_S1 option:selected").text(),
    game_R_S1: $("#game_R_S1 option:selected").text(),
    game_L_S2: $("#game_L_S2 option:selected").text(),
    game_R_S2: $("#game_R_S2 option:selected").text(),
    game_L_D1: $("#game_L_D1 option:selected").text(),
    game_R_D1: $("#game_R_D1 option:selected").text(),
    game_L_D2: $("#game_L_D2 option:selected").text(),
    game_R_D2: $("#game_R_D2 option:selected").text(),
    game_L_D3: $("#game_L_D3 option:selected").text(),
    game_R_D3: $("#game_R_D3 option:selected").text(),
    game_L_total: $("#game_L_total").text(),
    game_R_total: $("#game_R_total").text(),
    sender_name: $("#sender_name").val(),
    comment: $("#comment").val(),
    dummy: ""
  }
  if (data.sender_name == "") {
    data.sender_name = "<未入力>";
  }
  if (data.comment == "") {
    data.comment = "<コメントなし>";
  }
  var html = $('#mail_contents_template').render(data);

  // mailContents に表示されている HTML そのものを埋め込む (それがメールで送信される)
  $("#mail_contents").val(html);
  $("#mail_contents_view").html(html);
}

// 結果を送信する処理
function onSendReport() {
  // いったんテーブルを更新
  onTableUpdate();

  // 送信中の表示を出す
  $("#send_button").hide();
  $("#sending_status").show();
  $("#send_report_status").hide();

  // データを送信
  $.ajax({
    url: 'send-report.php',
    type: 'post',
    dataType: 'json', // 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
    data: {           // 送信データを指定
        mail_subject: $('#mail_subject').val(),
        mail_contents: $('#mail_contents').val(),
        match_name: $("#match_name").val(),
        match_image: $("#match_image").val(),
    },
  })
  // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
  .done(function (response) {
      $("#send_button").show();
      $("#sending_status").hide();
      $("#send_report_status").show();
  })
  // ・サーバからステータスコード400以上が返ってきたとき
  // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
  // ・通信に失敗したとき
  .fail(function () {
      $("#send_button").show();
      $("#sending_status").hide();
      $("#send_report_status").text("送信に失敗しました");
      $("#send_report_status").show();
  });
}
