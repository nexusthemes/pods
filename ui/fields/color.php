<?php
    wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );

    $attributes = array();
    $attributes[ 'type' ] = 'text';
    $attributes[ 'value' ] = $value;
    $attributes = PodsForm::merge_attributes( $attributes, $name, PodsForm::$field_type, $options );
?>
<input<?php PodsForm::attributes( $attributes, $name, PodsForm::$field_type, $options ); ?> />

<div id="color_<?php echo $attributes[ 'id' ]; ?>"></div>

<script type="text/javascript">
    if ( 'undefined' == pods_farbastic_changing )
        var pods_farbastic_changing = false;

    jQuery( function () {
        jQuery( '#color_<?php echo $attributes[ 'id' ]; ?>' ).hide();

        var pods_farbtastic_<?php echo pods_clean_name( $attributes[ 'id' ] ); ?> = jQuery.farbtastic(
            '#color_<?php echo $attributes[ 'id' ]; ?>',
            function ( color ) {
                pods_pickColor( '#<?php echo $attributes[ 'id' ]; ?>', color );
            }
        );

        jQuery( '#<?php echo $attributes[ 'id' ]; ?>' ).on( 'focus blur', function () {
            jQuery( '#color_<?php echo $attributes[ 'id' ]; ?>' ).slideToggle();
        } );

        jQuery( '#<?php echo $attributes[ 'id' ]; ?>' ).on( 'keyup', function () {
            var color = jQuery( this ).val();

            pods_farbastic_changing = true;

            if ( '' != color.replace( '#', '' ) && color.match( '#' ) )
                pods_farbtastic_<?php echo pods_clean_name( $attributes[ 'id' ] ); ?>.setColor( color );

            pods_farbastic_changing = false;
        } );

        if ( 'undefined' == pods_pickColor ) {
            function pods_pickColor ( id, color ) {
                if ( !pods_farbastic_changing )
                    jQuery( id ).val( color.toUpperCase() );
            }
        }
    } );
</script>