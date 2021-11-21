<?php
function add_stylesheet()
{
	wp_register_style('index', get_template_directory_uri() . '/dist/css/style.css',);
	wp_enqueue_style('index');
}

add_action('wp_enqueue_scripts', 'add_stylesheet');

function add_scripts()
{
	// 閉じBODYタグ前に出力
	wp_enqueue_script('main', get_template_directory_uri() . '/dist/js/index.js', '', '', true);
}
add_action('wp_print_scripts', 'add_scripts');
add_theme_support('post-thumbnails');
add_action('init', 'create_post_type');
function create_post_type()
{
	register_post_type(
		'product',
		array(
			'labels' => array(
				'name' => __('Product'),
				'singular_name' => __('Product')
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'author', 'trackbacks', 'comments', 'revisions', 'page-attributes')
		)
	);
}

/**
 * ページネーション出力関数
 * $paged : 現在のページ
 * $pages : 全ページ数
 * $range : 左右に何ページ表示するか
 * $show_only : 1ページしかない時に表示するかどうか
 */
function pagination($pages, $paged, $range = 2, $show_only = false)
{

	$pages = (int) $pages;    //float型で渡ってくるので明示的に int型 へ
	$paged = $paged ?: 1;       //get_query_var('paged')をそのまま投げても大丈夫なように

	//表示テキスト
	$text_first   = "« 最初へ";
	$text_before  = "";
	$text_next    = "";
	$text_last    = "最後へ »";

	if ($show_only && $pages === 1) {
		// １ページのみで表示設定が true の時
		echo '<div class="pagination"><span class="current pager">1</span></div>';
		return;
	}

	if ($pages === 1) return;    // １ページのみで表示設定もない場合

	if (1 !== $pages) {
		//２ページ以上の時
		//  echo '<div class="pagination"><span class="page_num">Page ', $paged ,' of ', $pages ,'</span>';
		if ($paged > $range + 1) {
			// 「最初へ」 の表示
			//   echo '<a href="', get_pagenum_link(1) ,'" class="first">', $text_first ,'</a>';
		}
		if ($paged > 1) {
			// 「前へ」 の表示
			echo '<a href="', get_pagenum_link($paged - 1), '" class="prev">', $text_before, '</a>';
		}
		for ($i = 1; $i <= $pages; $i++) {

			if ($i <= $paged + $range && $i >= $paged - $range) {
				// $paged +- $range 以内であればページ番号を出力
				if ($paged === $i) {
					echo '<span class="current pager">', $i, '</span>';
				} else {
					echo '<a href="', get_pagenum_link($i), '" class="pager">', $i, '</a>';
				}
			}
		}
		if ($paged < $pages) {
			// 「次へ」 の表示
			echo '<a href="', get_pagenum_link($paged + 1), '" class="next">', $text_next, '</a>';
		}
		if ($paged + $range < $pages) {
			// 「最後へ」 の表示
			//   echo '<a href="', get_pagenum_link( $pages ) ,'" class="last">', $text_last ,'</a>';
		}
		echo '</div>';
	}
};

//記事のアクセス数を表示
function getPostViews($postID)
{
	$count_key = 'post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if ($count == '') {
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
		return "0 View";
	}
	return $count . ' Views';
}

//記事のアクセス数を保存
function setPostViews($postID)
{
	$count_key = 'post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if ($count == '') {
		$count = 0;
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
	} else {
		$count++;
		update_post_meta($postID, $count_key, $count);
	}
}
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
