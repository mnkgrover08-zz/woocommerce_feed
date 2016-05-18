<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://company.com
 * @since             1.0.0
 * @package           company
 *
 * @wordpress-plugin
 * Plugin Name:       company
 * Plugin URI:        https://company.com
 * Description:       Generates your woocommerce Feed
 * Version:           1.0.0
 * Author:            Mayank Grover
 * Author URI:        https://company.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       company
 * Domain Path:       /languages
 */

add_action( 'init', 'pmg_rewrite_add_rewrites' );
function add_limit_query_var(){
        global $wp;
        $wp->add_query_var('limit');
}

function add_offset_query_var(){
        global $wp;
        $wp->add_query_var('offset');
}
add_filter('init', 'add_offset_query_var');
add_filter('init', 'add_limit_query_var');
function pmg_rewrite_add_rewrites()
{    
    add_rewrite_endpoint( 'company_feed',EP_ALL);
    add_rewrite_rule(
        '^company_feed/(\d+)/?$', // p followed by a slash, a series of one or more digets and maybe another slash
        'index.php?paged=$matches[1]',
        'top'
    );
    add_feed( 'company_feed', 'pmg_rewrite_json_feed' );
}


/*//add_action('pre_get_posts', 'myprefix_query_offset', 1 );
function myprefix_query_offset(&$query) {

    //Before anything else, make sure this is the right query...
    if ( ! $query->is_home() ) {
        return;
    }

    //First, define your desired offset...
    $offset = !empty(get_query_var('offset')) ? get_query_var('offset') : 0;
    //Next, determine how many posts per page you want (we'll use WordPress's settings)
    $limit = !empty(get_query_var('limit')) ? get_query_var('limit') : 1;
    if($limit > 20){
	$limit = 20;
    }

    //Next, detect and handle pagination...
    if ( $query->is_paged ) {

        //Manually determine page query offset (offset + current page (minus one) x posts per page)
        $page_offset = $offset + ( ($query->query_vars['paged']-1) * $limit );

        //Apply adjust page offset
        $query->set('offset', $page_offset );
	$query->set('posts_per_page', $limit );

    }
    else {

        //This is the first page. Just use the offset...
        $query->set('offset',$offset);
        $query->set('posts_per_page', $limit );

    }
}



add_filter('found_posts', 'company_adjust_offset_pagination', 1, 2 );
function company_adjust_offset_pagination($found_posts, $query) {

    //Define our offset again...
    $offset = !empty(get_query_var('offset')) ? get_query_var('offset') : 0;
    //Ensure we're modifying the right query object...
    if ( $query->is_home() ) {
        //Reduce WordPress's found_posts count by the offset... 
        return $found_posts - $offset;
    }
    return $found_posts;
}*/


function pmg_rewrite_json_feed()
{
    $full_product_list = array();

    $offset = !empty(get_query_var('offset')) ? get_query_var('offset') : 0;
    $limit = !empty(get_query_var('limit')) ? get_query_var('limit') : 1;
    if($limit > 20){
    $limit = 20;
    }

	$loop = new WP_Query( array( 'post_type' => array('product') , 'posts_per_page'=>$limit ,'offset'=>$offset  ) );
	$loop1 = new WP_Query( array( 'post_type' => array('product'),'posts_per_page'=>-1 ) );
	$count = $loop1->found_posts;
    while ( $loop->have_posts() ) : $loop->the_post();
        
            $theid = get_the_ID();
        
            $product = new WC_Product($theid);
        
            $sku = get_post_meta($theid, '_sku', true );
            
            $stock_status = get_post_meta($theid, '_stock_status', true );
            
            $downloadable = get_post_meta($theid, '_downloadable', true );
            
            $args = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' =>'any', 'post_parent'=>$theid );
            $attachments = get_posts( $args );
            if ( $attachments ) {
                $x = 1;
                foreach ( $attachments as $attachment ) {

                    $images[$x] = wp_get_attachment_url( $attachment->ID );
                            $x = $x + 1;

                }
            }
            
            $img_src = wp_get_attachment_image_src( get_post_thumbnail_id($theid))[0];
            
            $virtual = get_post_meta($theid, '_virtual', true );
            
            $tax_status = get_post_meta($theid, '_tax_status', true );
            
            $weight = get_post_meta($theid, '_weight', true );
            
            $length = get_post_meta($theid, '_length', true );
            
            $width = get_post_meta($theid, '_width', true );
            
            $height = get_post_meta($theid, '_height', true );
            
            $stock = get_post_meta($theid, '_stock', true );
            
            $product_attributes = get_post_meta($theid, '_product_attributes', true );
            
            $regular_price = get_post_meta($theid, '_regular_price', true );
            
            $sale_price = get_post_meta($theid, '_sale_price', true );
            
            $thetitle = get_the_title();

            $variantions = new WP_Query( array( 'post_parent'=>$theid,'post_type' => array('product_variation'),'posts_per_page'=>-1 ) );
            
            if ($variantions->have_posts()) {
                
                while ( $variantions->have_posts() ) : $variantions->the_post();
            
                $vtheid = get_the_ID();
            
                $vproduct = new WC_Product($vtheid);
            
                $vsku = get_post_meta($vtheid, '_sku', true );
                
                $vstock_status = get_post_meta($vtheid, '_stock_status', true );
                
                $vdownloadable = get_post_meta($vtheid, '_downloadable', true );    

                $vargs = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' =>'any', 'post_parent'=>$vtheid );
                $vattachments = get_posts( $vargs );
                if ( $vattachments ) {
                    $x = 1;
                    foreach ( $vattachments as $vattachment ) {

                        $vimages[$x] = wp_get_attachment_url( $vattachment->ID );
                                $x = $x + 1;

                    }
                }
                
                $vimg_src = wp_get_attachment_image_src( get_post_thumbnail_id($vtheid))[0];
                
                $vvirtual = get_post_meta($vtheid, '_virtual', true );
                
                $vtax_status = get_post_meta($vtheid, '_tax_status', true );
                
                $vweight = get_post_meta($vtheid, '_weight', true );
                
                $vlength = get_post_meta($vtheid, '_length', true );
                
                $vwidth = get_post_meta($vtheid, '_width', true );
                
                $vheight = get_post_meta($vtheid, '_height', true );
                
                $vstock = get_post_meta($vtheid, '_stock', true );
                
                $vproduct_attributes = get_post_meta($vtheid, '_product_attributes', true );
                
                $vregular_price = get_post_meta($vtheid, '_regular_price', true );
                
                $vsale_price = get_post_meta($vtheid, '_sale_price', true );
                
                $vthetitle = get_the_title();

                $vvariant_name = get_post_meta($vtheid, 'attribute_size', true );

                $variants[] = array('product_title'=>$vthetitle, 'sku'=>$vsku, 'product_image' => $vimg_src,'post_id' => $vtheid,'stock_status'=>$vstock_status,'downloadable'=>$vdownloadable,'variant_name'=>$vvariant_name,'virtual'=>$vvirtual,'stock'=>$vstock,'tax_status'=>$vtax_status,'weight'=>$vweight,'length'=>$vlength,'width'=>$vwidth,'height'=>$vheight,'product_attributes'=>$vproduct_attributes,'regular_price'=>$vregular_price,'sale_price'=>$vsale_price,'content'=>$vproduct->post->post_content,'description'=>$vproduct->post->post_excerpt);
                endwhile; wp_reset_query();

            }else{
                $variants = array();
            }



            // ****** Some error checking for product database *******
                    // check if variation sku is set
                    if ($sku == '') {
                        if ($parent_id == 0) {
                            // Remove unexpected orphaned variations.. set to auto-draft
                            $false_post = array();
                            $false_post['ID'] = $theid;
                            $false_post['post_status'] = 'auto-draft';
                            wp_update_post( $false_post );
                            if (function_exists(add_to_debug)) add_to_debug('false post_type set to auto-draft. id='.$theid);
                        } else {
                            // there's no sku for this variation > copy parent sku to variation sku
                            // & remove the parent sku so the parent check below triggers
                            $sku = get_post_meta($parent_id, '_sku', true );
                            if (function_exists(add_to_debug)) add_to_debug('empty sku id='.$theid.'parent='.$parent_id.'setting sku to '.$sku);
                            update_post_meta($theid, '_sku', $sku );
                            update_post_meta($parent_id, '_sku', '' );
                        }
                    }
            // ****************** end error checking *****************



            if (!empty($sku) && !isset($full_product_list[$theid])) {
                $full_product_list[$theid] = array('product_title'=>$thetitle, 'sku'=>$sku, 'product_image' => $img_src,'post_id' => $theid,'stock_status'=>$stock_status,'downloadable'=>$downloadable,'virtual'=>$virtual,'stock'=>$stock,'tax_status'=>$tax_status,'weight'=>$weight,'length'=>$length,'width'=>$width,'height'=>$height,'product_attributes'=>$product_attributes,'regular_price'=>$regular_price,'sale_price'=>$sale_price,'content'=>$product->post->post_content,'description'=>$product->post->post_excerpt,'variants'=>$variants);
            }





        /*// its a variable product

        if( get_post_type() == 'product_variation' ){
            

            $parent_id = wp_get_post_parent_id($theid);
            
            $parent_product = new WC_Product($parent_id);
            
            $sku = get_post_meta($theid, '_sku', true );
            
            $img_src = wp_get_attachment_image_src( get_post_thumbnail_id($parent_id,'full'))[0];

            $args = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' =>'any', 'post_parent' => $parent_id );
            $attachments = get_posts( $args );
            if ( $attachments ) {
                $x = 1;
                foreach ( $attachments as $attachment ) {

                    $images[$x] = wp_get_attachment_url( $attachment->ID );
                            $x = $x + 1;

                }
            }

            $thetitle = get_the_title( $parent_id);
            
            $stock_status = get_post_meta($theid, '_stock_status', true );
            
            $downloadable = get_post_meta($theid, '_downloadable', true );
            
            $virtual = get_post_meta($theid, '_virtual', true );
            
            $tax_status = get_post_meta($theid, '_tax_status', true );
            
            $weight = get_post_meta($theid, '_weight', true );
            
            $length = get_post_meta($theid, '_length', true );
            
            $stock = get_post_meta($theid, '_stock', true );
            
            $width = get_post_meta($theid, '_width', true );
            
            $height = get_post_meta($theid, '_height', true );

            $variant_name = get_post_meta($theid, 'attribute_size', true );
            
            $product_attributes = get_post_meta($theid, '_product_attributes', true );
            
            $regular_price = get_post_meta($theid, '_regular_price', true );
            
            $sale_price = get_post_meta($theid, '_sale_price', true );

            $sku_parent = get_post_meta($parent_id, '_sku', true );
            
            $thetitle_parent = get_the_title( $parent_id);
            
            $stock_status_parent = get_post_meta($parent_id, '_stock_status', true );
            
            $downloadable_parent = get_post_meta($parent_id, '_downloadable', true );
            
            $virtual_parent = get_post_meta($parent_id, '_virtual', true );
            
            $tax_status_parent = get_post_meta($parent_id, '_tax_status', true );
            
            $weight_parent = get_post_meta($parent_id, '_weight', true );
            
            $length_parent = get_post_meta($parent_id, '_length', true );
            
            $stock_parent = get_post_meta($parent_id, '_stock', true );
            
            $width_parent = get_post_meta($parent_id, '_width', true );
            
            $height_parent = get_post_meta($parent_id, '_height', true );
            
            $product_attributes_parent = get_post_meta($parent_id, '_product_attributes', true );
            
            $regular_price_parent = get_post_meta($parent_id, '_regular_price', true );
            
            $sale_price_parent = get_post_meta($parent_id, '_sale_price', true );

            // ****** Some error checking for product database *******
                    // check if variation sku is set
                    if ($sku == '') {
                        if ($parent_id == 0) {
                            // Remove unexpected orphaned variations.. set to auto-draft
                            $false_post = array();
                            $false_post['ID'] = $theid;
                            $false_post['post_status'] = 'auto-draft';
                            wp_update_post( $false_post );
                            if (function_exists(add_to_debug)) add_to_debug('false post_type set to auto-draft. id='.$theid);
                        } else {
                            // there's no sku for this variation > copy parent sku to variation sku
                            // & remove the parent sku so the parent check below triggers
                            $sku = get_post_meta($parent_id, '_sku', true );
                            if (function_exists(add_to_debug)) add_to_debug('empty sku id='.$theid.'parent='.$parent_id.'setting sku to '.$sku);
                            update_post_meta($theid, '_sku', $sku );
                            update_post_meta($parent_id, '_sku', '' );
                        }
                    }
            // ****************** end error checking *****************
            if (array_key_exists($parent_id, $full_product_list)==false) {
                $full_product_list[$parent_id] = array('product_title'=>$thetitle, 'sku_parent'=>$sku_parent, 'product_image' => $img_src ,'post_id' => $parent_id,'stock_status_parent'=>$stock_status_parent,'downloadable_parent'=>$downloadable_parent,'virtual_parent'=>$virtual_parent,'stock_parent'=>$stock_parent,'tax_status_parent'=>$tax_status_parent,'weight_parent'=>$weight_parent,'length_parent'=>$length_parent,'width_parent'=>$width_parent,'height_parent'=>$height_parent,'product_attributes_parent'=>$product_attributes_parent,'regular_price_parent'=>$regular_price_parent,'sale_price_parent'=>$sale_price_parent,'content_parent'=>$product->post->post_content,'description_parent'=>$product->post->post_excerpt,'variants'=>array());          
            }
            if (!empty($sku)) { 
                $full_product_list[$parent_id]['variants'][] = array('product_title'=>$thetitle, 'variant_name' => $variant_name ,'sku'=>$sku, 'post_id' => $theid,'stock_status'=>$stock_status,'downloadable'=>$downloadable,'virtual'=>$virtual,'stock'=>$stock,'tax_status'=>$tax_status,'weight'=>$weight,'length'=>$length,'width'=>$width,'height'=>$height,'regular_price'=>$regular_price,'sale_price'=>$sale_price,'content'=>$product->post->post_content,'description'=>$product->post->post_excerpt);
            }
            // its a simple product
        } else {
            
            
        
        }*/
    endwhile; wp_reset_query();
    // sort into alphabetical order, by title
    //sort($full_product_list);
    header('Content-Type: text/plain');
    //echo json_encode($gallery);
    echo json_encode( array('total_count' => $count,'products' => $full_product_list ));
}
register_activation_hook( __FILE__, 'pmg_rewrite_activation' );
function pmg_rewrite_activation()
{
    pmg_rewrite_add_rewrites();
    flush_rewrite_rules();
}

function company_tab() {
    add_menu_page(
        __( 'company', 'company' ),
        'company',
        'manage_options',
        'company/company-admin.php',
        '',
        plugins_url( 'company/images/k-icon.png' ),
        6
    );
}
add_action( 'admin_menu', 'company_tab' );

