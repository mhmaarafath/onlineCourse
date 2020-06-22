</div>
</body>
</html>
<script>
$(document).ready(function(){
    invoice_id = $('[name="invoice_id"]').val();  

    $("select").hide();
    $("select").siblings("div").hide();
    
    $('input[type=checkbox]').each(function () {
      if($(this).prop("checked") == true){
        $(this).siblings("select").show();
        $(this).siblings("div").show();
        // batch_id = $(this).siblings("select").val();
        // $(this).siblings("div").attr('id', batch_id);
        // $.ajax({  
        //   url:"load_data.php",  
        //   method:"POST",  
        //   data:{action:"search", batch_id:batch_id},  
        //   cache: false,
        //   success:function(data){
            
        //     $('#'+batch_id).html(data);
        //   }
        // });                
      } else if($(this).prop("checked") == false){
        $(this).siblings("select").hide();
        $(this).siblings("div").hide();
      }
    });


    // if($('[type="checkbox"]').is(':checked')){
    //   $(this).siblings("select").show();
    //   $(this).siblings("div").show();
    // } else {
    //   $(this).siblings("select").hide();
    //   $(this).siblings("div").hide();
    // }


    $('[name="batch_id"]').change(function(){
      subject_id = $(this).siblings('[type="checkbox"]').val();
      $(this).siblings("select").attr("required", "true");
      batch_id = $(this).val();  
      $.ajax({  
          url:"load_data.php",  
          method:"POST",  
          data:{action:"insert", batch_id:batch_id, invoice_id:invoice_id, subject_id:subject_id},  
          cache: false,
          success:function(data){
            window.location.href = "index.php";
          }
      });   
    });


    $('[type="checkbox"]').click(function(){
      subject_id = $(this).val();
      if($(this).prop("checked") == true){
        $(this).siblings("select").show();
        $(this).siblings("select").attr("required", "true");
      }
      else if($(this).prop("checked") == false){
        batch_id = $(this).siblings("select").val();  
        $(this).siblings("select").hide();
        $(this).siblings("select").attr("required", "false");
        $.ajax({  
          url:"load_data.php",  
          method:"POST",  
          data:{action:"delete", batch_id:batch_id, invoice_id:invoice_id, subject_id:subject_id},  
          cache: false,
          success:function(data){
            window.location.href = "index.php";            
          }
        });   
      }
    });


  var total_amount = function(){
    total = 0;
    $('[name="payment_amount"]').each(function(){
      total += Number($(this).val());
    });
    $('#total').val(total);  
    $('[name="amount"]').val(total);
  }

  total_amount();

  $('[name="payment_amount"]').on("blur paste keyup", function() {
    id = $(this).attr('id');
    payment_amount = $(this).val();
    $.ajax({  
      async:false,
      url:"load_data.php",  
      method:"POST",  
      data:{action:"update", id:id, payment_amount:payment_amount},  
      cache: false,
      success:function(data){
        $(this).attr('value') = data;
      }
    });
    total_amount();   
  });
  
});




</script>

