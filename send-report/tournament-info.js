// tournament-info.js

var teamInfo = {
	man: [
		"本社-A", "本社-B", "本社-C", "本社-D", "本社-E",
		"品川シーサイド-A", "品川シーサイド-B", "品川シーサイド-C",
		"厚木-A", "厚木-B",
		"SSMD・DXJ-A", "SSMD・DXJ-B", "SSMD・DXJ-C",
		"ソニー生命", "SFH-A", "SFH-B",
		"木更津-A", "木更津-B",
		"SGMO-A", "SGMO-B",
		"JDI", "SLSI", "SIE", "SMOJ"
	],
	woman: [ 
		"本社-A", "本社-B",
		"品川シーサイド",
		"SFH", "SGMO", "SGS"
	],
	senior: [ 
		"本社-A", "本社-B", "厚木", "SMOJ", "プラザスタイル", "VAIO"
	],
};

var leagueInfo = {
	man: [
		{
			label: "男子予選", leagues: [
				{ name: "man_preliminary_A", text: "予選Aリーグ" },
				{ name: "man_preliminary_B", text: "予選Bリーグ" },
				{ name: "man_preliminary_C", text: "予選Cリーグ" },
				{ name: "man_preliminary_D", text: "予選Dリーグ" },
				{ name: "man_preliminary_E", text: "予選Eリーグ" },
				{ name: "man_preliminary_F", text: "予選Fリーグ" },
				{ name: "man_preliminary_G", text: "予選Gリーグ" },
				{ name: "man_preliminary_H", text: "予選Hリーグ" },
			]
		},
		{
			label: "男子本戦", leagues: [
				{ name: "man_final_1", text: "決勝1位トーナメント" },
				{ name: "man_final_2", text: "決勝2位トーナメント" },
				{ name: "man_final_3", text: "決勝3位トーナメント" },
				{ name: "man_final_4", text: "決勝4位トーナメント" },
				{ name: "man_final_5", text: "決勝5位トーナメント" },
				{ name: "man_final_6", text: "決勝6位トーナメント" },
			]
		},
	],
	woman: [
		{
			label: "女子", leagues: [
				{ name: "woman_league", text: "女子リーグ" },
			]
		},
	],
	senior: [
		{
			label: "シニア", leagues: [
				{ name: "senior_league", text: "シニアリーグ" },
			]
		},
	],
};
