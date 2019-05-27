$(function(){


    //clases propias de validacion
    jQuery.validator.addClassRules({
          val_nombre: {
                required: true,
                minlength: 2,
                maxlength: 150
          },
          val_numero: {
            required: true,
            digits: true,
            minlength: 1,
            maxlength: 5
          }
        });


    jQuery.validator.addMethod("lettersonly", function(value, element) {
		return this.optional(element) || /^[a-z ]+$/i.test(value);
	} , "Solo letras." );

	// custom code to for greater than
	jQuery.validator.addMethod('greaterThan', function(value, element, param) {
		return ( value > param );
	}, "Debes ingresar un valor mayor." );

	// custom code for lesser than
	jQuery.validator.addMethod('lesserThan', function(value, element, param) {
		return ( value < param );
	}, 'Debes ingresar un valor menor.' );



    $("#form_"+$("#tabla_bd").val() ).validate({
                errorElement: "em",
                errorPlacement: function ( error, element ) {
                        // Add the `help-block` class to the error element
                        error.addClass( "help-block" );
                        
                        if ( element.prop( "type" ) === "checkbox" ) {
                            error.insertAfter( element.parent( "label" ) );
                        } else {
                            error.insertAfter( element );
                        }
                    },
                highlight: function ( element, errorClass, validClass ) {
                        $( element ).parents( ".validar" ).addClass( "has-error" ).removeClass( "has-success" );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).parents( ".validar" ).addClass( "has-success" ).removeClass( "has-error" );
                },
                submitHandler: function(form) {
                    $(form).ajaxSubmit();
                }


    });




});
