$(document).ready(function() {
    $('#sign_up').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            name: {
                message: 'Entered name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The name  is required and can\'t be empty'
                    },
                    stringLength: {
                        min: 3,
                        max: 50,
                        message: 'The name is required to be more than 3 and less than 50 characters long'
                    }
                }
            },
              lastname: {
                message: 'Entered lastname is not valid',
                validators: {
                    notEmpty: {
                        message: 'The lastname  is required and can\'t be empty'
                    },
                    stringLength: {
                        min: 1,
                        max: 50,
                        message: 'The lastname is required to be more than 1 and less than 50 characters long'
                    }
                }
            },
             answer: {
                message: 'Entered answer is not valid',
                validators: {
                    notEmpty: {
                        message: 'The answer  is required and can\'t be empty'
                    },
                    stringLength: {
                        min: 1,
                        max: 100,
                        message: 'The answer is required to be more than 1 and less than 100 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9iİüÜöÖşŞçÇğĞ.;*-+!@#$%^&\s]+$/,
                        message: 'The answer can only consist of alphabetical characters'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email address is required and can\'t be empty'
                    },
                    emailAddress: {
                        message: 'The input is not a valid email address'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The password is required and can\'t be empty'
                    },
                    identical: {
                        field: 'confirmPassword',
                        message: 'The password and its confirm are not the same'
                    }
                }
            },
            confirmPassword: {
                validators: {
                    notEmpty: {
                        message: 'The confirm password is required and can\'t be empty'
                    },
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    }
                }
            }
        }
    });
});

$(document).ready(function() {
    $('#sign_in').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email address is required and can\'t be empty'
                    },
                    emailAddress: {
                        message: 'The input is not a valid email address'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The password is required and can\'t be empty'
                    }
                }
            }
        }
    });
});

