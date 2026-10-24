<?php
defined( 'ABSPATH' ) || exit;

/**
 * Buffer article view counters in the object cache and flush to the
 * existing post/term meta keys only when a threshold or age is hit.
 * Keys and stored formats stay the same: views, trending, last_update.
 */
if ( ! defined( 'KAYAN_VIEW_FLUSH_EVERY' ) ) {
	define( 'KAYAN_VIEW_FLUSH_EVERY', 10 );
}
if ( ! defined( 'KAYAN_VIEW_FLUSH_AFTER' ) ) {
	define( 'KAYAN_VIEW_FLUSH_AFTER', 600 );
}

if ( ! function_exists( 'kayan_views_cache_key' ) ) {
	function kayan_views_cache_key( $kind, $id ) {
		return 'kayan_viewbuf_' . $kind . '_' . (int) $id;
	}
}

if ( ! function_exists( 'kayan_views_cache_incr' ) ) {
	function kayan_views_cache_incr( $key ) {
		$n = wp_cache_incr( $key );
		if ( false !== $n ) {
			return (int) $n;
		}
		wp_cache_add( $key, 0 );
		$n = wp_cache_incr( $key );
		if ( false !== $n ) {
			return (int) $n;
		}
		$cur = (int) wp_cache_get( $key );
		$cur++;
		wp_cache_set( $key, $cur );
		return $cur;
	}
}

if ( ! function_exists( 'kayan_views_should_flush' ) ) {
	function kayan_views_should_flush( $kind, $id, $pending ) {
		if ( $pending >= (int) KAYAN_VIEW_FLUSH_EVERY ) {
			return true;
		}
		$since_key = kayan_views_cache_key( $kind . '_since', $id );
		$started   = (int) wp_cache_get( $since_key );
		if ( $started <= 0 ) {
			wp_cache_add( $since_key, time() );
			return false;
		}
		return ( time() - $started ) >= (int) KAYAN_VIEW_FLUSH_AFTER;
	}
}

if ( ! function_exists( 'kayan_views_take_pending' ) ) {
	function kayan_views_take_pending( $kind, $id ) {
		$key     = kayan_views_cache_key( $kind, $id );
		$pending = (int) wp_cache_get( $key );
		if ( $pending <= 0 ) {
			return 0;
		}
		$left = wp_cache_decr( $key, $pending );
		if ( false === $left ) {
			wp_cache_delete( $key );
		}
		wp_cache_delete( kayan_views_cache_key( $kind . '_since', $id ) );
		return $pending;
	}
}

if ( ! function_exists( 'kayan_views_write_views' ) ) {
	function kayan_views_write_views( $post_id, $add ) {
		$post_id = (int) $post_id;
		$add     = (int) $add;
		if ( $post_id <= 0 || $add <= 0 ) {
			return;
		}
		$current = (int) get_post_meta( $post_id, 'views', true );
		update_post_meta( $post_id, 'views', $current + $add );
	}
}

if ( ! function_exists( 'kayan_views_write_trending' ) ) {
	function kayan_views_write_trending( $post_id, $add ) {
		$post_id = (int) $post_id;
		$add     = (int) $add;
		if ( $post_id <= 0 || $add <= 0 ) {
			return;
		}
		$today    = date( 'd-m-Y' );
		$last     = get_post_meta( $post_id, 'last_update', true );
		$trending = (int) get_post_meta( $post_id, 'trending', true );
		if ( $last !== $today ) {
			$trending = 0;
		}
		update_post_meta( $post_id, 'trending', $trending + $add );
		if ( $last !== $today ) {
			update_post_meta( $post_id, 'last_update', $today );
		}
	}
}

if ( ! function_exists( 'kayan_views_write_term_views' ) ) {
	function kayan_views_write_term_views( $term_id, $add ) {
		$term_id = (int) $term_id;
		$add     = (int) $add;
		if ( $term_id <= 0 || $add <= 0 ) {
			return;
		}
		$current = (int) get_term_meta( $term_id, 'views', true );
		update_term_meta( $term_id, 'views', $current + $add );
	}
}

if ( ! function_exists( 'kayan_views_bump' ) ) {
	function kayan_views_bump( $kind, $id ) {
		$id = (int) $id;
		if ( $id <= 0 ) {
			return;
		}
		if ( ! wp_using_ext_object_cache() ) {
			if ( 'views' === $kind ) {
				kayan_views_write_views( $id, 1 );
			} elseif ( 'trending' === $kind ) {
				kayan_views_write_trending( $id, 1 );
			} elseif ( 'term_views' === $kind ) {
				kayan_views_write_term_views( $id, 1 );
			}
			return;
		}
		$pending = kayan_views_cache_incr( kayan_views_cache_key( $kind, $id ) );
		if ( ! kayan_views_should_flush( $kind, $id, $pending ) ) {
			return;
		}
		$lock = kayan_views_cache_key( $kind . '_flush', $id );
		if ( ! wp_cache_add( $lock, 1, '', 15 ) ) {
			return;
		}
		$pending = kayan_views_take_pending( $kind, $id );
		if ( $pending <= 0 ) {
			wp_cache_delete( $lock );
			return;
		}
		if ( 'views' === $kind ) {
			kayan_views_write_views( $id, $pending );
		} elseif ( 'trending' === $kind ) {
			kayan_views_write_trending( $id, $pending );
		} elseif ( 'term_views' === $kind ) {
			kayan_views_write_term_views( $id, $pending );
		}
		wp_cache_delete( $lock );
	}
}

add_action(
	'BeforeBlade_single',
	function() {
		wp_reset_query();
		global $post;
		if ( ! isset( $post ) || ! is_object( $post ) || empty( $post->ID ) ) {
			return;
		}
		kayan_views_bump( 'views', (int) $post->ID );
	}
);
