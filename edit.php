<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_boards/edit.php,v 1.11 2008/07/31 23:31:38 wjames5 Exp $
 * Copyright (c) 2004 bitweaver Messageboards
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * @package boards
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'boards' );

if( isset( $_REQUEST['bitboard']['board_id'] ) ) {
	$_REQUEST['b'] = $_REQUEST['bitboard']['board_id'];
}

require_once(BOARDS_PKG_PATH.'lookup_inc.php' );

//must be owner or admin to edit an existing board
if( $gContent->isValid() ) {
	$gContent->verifyEditPermission();
} else {
	$gBitSystem->verifyPermission( 'p_boards_edit' );
}

// If we are in preview mode then preview it!
if( isset( $_REQUEST["preview"] ) ) {
	$gBitSmarty->assign('preview', 'y');
	$previewHash = array_merge( $_REQUEST, $_REQUEST['bitboard'] );
	$gContent->preparePreview( $previewHash );
	$gContent->invokeServices( 'content_preview_function' );
} else {
	$gContent->invokeServices( 'content_edit_function' );
}

// Pro
// Check if the page has changed
if( !empty( $_REQUEST["save_bitboard"] ) ) {
	// merge our arrays so our storage hash works with LibertyContent storage of LibertyContent add ons.
	$storeHash = array_merge( $_REQUEST, $_REQUEST['bitboard'] );
	// Check if all Request values are delivered, and if not, set them
	// to avoid error messages. This can happen if some features are
	// disabled
	if( $gContent->store( $storeHash ) ) {
		$gContent->storePreference( 'board_sync_list_address', (!empty( $_REQUEST['bitboardconfig']['board_sync_list_address'] ) ?  $_REQUEST['bitboardconfig']['board_sync_list_address'] : NULL) );
		header( "Location: ".$gContent->getDisplayUrl() );
		die;
	} else {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

// Display the template
$gBitSystem->display( 'bitpackage:boards/board_edit.tpl', tra('Board') , array( 'display_mode' => 'edit' ));
?>
