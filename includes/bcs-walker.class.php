<?php
/**
 * Better Category Selector for WooCommerce Walker Class
 *
 * Copyright: (c) 2022, HisDesigns LLC

 * Taxonomy API: HD_BCS_Walker_Category_Checklist class
 *
 * Remake of Walker_Category_Checklist to add bcs-has-child
 * class to items with children.
 *
 * @since 1.0
 *
 * @see Walker
 * @see Walker_Category_Checklist
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 */

class HD_BCS_Walker_Category_Checklist extends Walker {
    public $tree_type = 'category';
    public $db_fields = array(
        'parent' => 'parent',
        'id'     => 'term_id',
    ); // TODO: Decouple this.
 
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker:start_lvl()
     *
     * @since 2.5.1 of Walker_Category_Checklist
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent  = str_repeat( "\t", $depth );
        $output .= "$indent<ul class='children'>\n";
    }
 
    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 2.5.1 of Walker_Category_Checklist
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent  = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }
 
    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 2.5.1 of Walker_Category_Checklist
     *
     * @param string  $output   Used to append additional content (passed by reference).
     * @param WP_Term $category The current term object.
     * @param int     $depth    Depth of the term in reference to parents. Default 0.
     * @param array   $args     An array of arguments. @see wp_terms_checklist()
     * @param int     $id       ID of the current term.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {

        if ( empty( $args['taxonomy'] ) ) {
            $taxonomy = 'category';
        } else {
            $taxonomy = $args['taxonomy'];
        }
 
        if ( 'category' === $taxonomy ) {
            $name = 'post_category';
        } else {
            $name = 'tax_input[' . $taxonomy . ']';
        }
 
        $args['popular_cats'] = ! empty( $args['popular_cats'] ) ? array_map( 'intval', $args['popular_cats'] ) : array();

        $class = in_array( $category->term_id, $args['popular_cats'], true ) ? ' class="popular-category"' : '';
 
        $args['selected_cats'] = ! empty( $args['selected_cats'] ) ? array_map( 'intval', $args['selected_cats'] ) : array();
 
        if ( ! empty( $args['list_only'] ) ) {
            $aria_checked = 'false';
            $inner_class  = 'category';
 
            if ( in_array( $category->term_id, $args['selected_cats'], true ) ) {
                $inner_class .= ' selected';
                $aria_checked = 'true';
            }
 
            $output .= "\n" . '<li' . $class . '>' .
                '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                ' tabindex="0" role="checkbox" aria-checked="' . $aria_checked . '">' .
                /** This filter is documented in wp-includes/category-template.php */
                esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</div>';
        } else {
            $is_selected = in_array( $category->term_id, $args['selected_cats'], true );
            $is_disabled = ! empty( $args['disabled'] );
 
            $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' .
                checked( $is_selected, true, false ) .
                disabled( $is_disabled, true, false ) . ' /> ' .
                /** This filter is documented in wp-includes/category-template.php */
                esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</label>';
        }
    }
 
    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @since 2.5.1 of Walker_Category_Checklist
     *
     * @param string  $output   Used to append additional content (passed by reference).
     * @param WP_Term $category The current term object.
     * @param int     $depth    Depth of the term in reference to parents. Default 0.
     * @param array   $args     An array of arguments. @see wp_terms_checklist()
     */
    public function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }

    /**
     * Add Max Depth to This Walker.
     *
     * $max_depth = -1 means flatly display every element.
     * $max_depth = 0 means display all levels.
     * $max_depth > 0 specifies the number of display levels.
     *
     * @since 1.0.3
     *
     * @param array $elements  An array of elements.
     * @param int   $max_depth The maximum hierarchical depth.
     * @param mixed ...$args   Optional additional arguments.
     * @return string The hierarchical item output.
     */
    public function walk( $elements, $max_depth, ...$args ) {
      return parent::walk($elements, 1, ...$args);
    }

}