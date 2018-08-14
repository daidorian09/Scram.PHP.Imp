<?php 
require_once("includes/Header.php"); 
?>

<body>
<?php
require_once("includes/Navbar.php");
require_once("CSRFToken.php");
?>
    <div class="container" style="margin-top: 50px;">
        <div class="row">
            <div class="col-lg-7 col-lg-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sign up</h3>
                    </div>

                    <div class="panel-body">
                        <form id="sign_up"  enctype="application/x-www-form-urlencoded"> 
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" placeholder="Name" />
                            </div>

                             <div class="form-group">
                                <input type="text" class="form-control" name="lastname" placeholder="Lastname" />
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="email" placeholder="Email" />
                            </div>

                             <div class="form-group">
                              <?php 
                              require_once("GetQuestions.php");
                              ?>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control"  name="answer" placeholder="Answer" />
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control"  name="password" placeholder="Password" />
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm password" />
                            </div>

                              <div class="form-group">
                                <input type="hidden" class="form-control" name="csrf_token" value="<?=CSRFToken::generate_token();?>"/>
                             </div>

                            <div class="form-group">
    						 <button type="submit" id="submit" name="submit"  class="btn btn-primary  col-lg-6 col-lg-offset-3">Sign up</button>
                            </div>


                              <div id="response" hidden style="margin-top:62px;">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function()
    {
       $('#sign_up').bootstrapValidator().on('submit', function (e)
       {
        if (!e.isDefaultPrevented()) 
        {
           $.ajax
            ({  
                type    : "POST",
                url :'sign_up.php',
                data    :$('#sign_up').serialize(),
                success :function(data)
                {
                       var response = JSON.parse(data);
                       if(response.check == true)
                       {
                            $("#response").addClass("alert alert-success")+$("#response").html("<h4 class='col-md-offset-4'><strong>Sign-up is completed.</strong></h4>").show();
                       }
                       else
                       {
                            $("#response").addClass("alert alert-danger")+$("#response").html("<h4 class='col-md-offset-4'><strong>.</strong>Sign-up is incompleted.</h4>").show();
                              setTimeout("location.href = 'SignUp.php';",1000);
                       }       
                },
            })
        }
        })
    })
</script>
<?php
require_once("includes/Footer.php");
?>