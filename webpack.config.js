const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const globule = require("globule");
const HtmlWebpackPlugin = require("html-webpack-plugin");
	const app = {
	// 読み込み先（srcの中のjsフォルダのinit.tsを読み込む）
	entry: path.resolve(__dirname, "src/js/index.ts"),
	//出力先（distの中のjsフォルダへinit.jsを出力）
	output: {
		filename: "./js/index.js",
		path: path.resolve(__dirname, "dist")
	},
	stats: {
		children: true
	},
	module: {
		rules: [
			{
				test: /\.ts$/,
				use: "ts-loader",
				exclude: /node_modules/
			},
			{
				test: /\.scss$/, 
				use: [
					{
						loader: MiniCssExtractPlugin.loader
					},
					{
						loader: "css-loader",
						options: {
							url: false,
							sourceMap: true,
							importLoaders: 2
						}
					},
					{
						loader: "postcss-loader",
						options: {
							sourceMap: true,
							postcssOptions: {
								plugins: [require("autoprefixer")({ grid: true })]
							}
						}
					},
					{
						loader: "sass-loader",
						options: {
							implementation: require("sass"),
							sassOptions: {
								fiber: require("fibers")
							},
							sourceMap: true
						}
					}
				]
			},
			{
				test: /\.pug$/,
				use: [
					{
						loader: "pug-loader",
						options: {
							pretty: true,
							filters: {
								php: (text) => {
									text =  "<?php " + text + " ?>";
									return text;
								}
							}
						}
					}
				]
			}
		]
	},
	target: ["web", "es5"],
	resolve: {
		// 拡張子を配列で指定
		extensions: [".ts", ".js"]
	},
	//プラグインの設定
	plugins: [
		new CleanWebpackPlugin({ // dist内の不要なファイルやフォルダを消す
		}),
		new MiniCssExtractPlugin({ // distの中にあるcssフォルダにstyle.cssを出力
			filename: "./css/style.css"
		})
	],
	//source-map タイプのソースマップを出力
	devtool: "source-map",
	// node_modules を監視（watch）対象から除外
	watchOptions: {
		ignored: /node_modules/ //正規表現で指定
	}
};

//srcフォルダからpngを探す
const templates = globule.find("./src/templates/**/*.pug", {
	ignore: ["./src/templates/**/_*.pug"]
});

//pugファイルがある分だけhtmlに変換する
templates.forEach((template) => {
	const fileName = template.replace("./src/templates/", "").replace(".pug", ".php");
	app.plugins.push(
		new HtmlWebpackPlugin({
			filename: `${fileName}`,
			template: template,
			inject: false, //false, head, body, trueから選べる
			minify: false //本番環境でも圧縮しない
		})
	);
});

module.exports = app;
