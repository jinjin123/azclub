Drupal.behaviors.PharmaTheme = {
  attach: function(context, settings) {
    var $ = jQuery.noConflict();
    $(".az-search-button").click(function () {
      console.log("aa")
    })
  }
};
  //http://localhost:30000/Clinical/search-result?field_az_conditions_term_target_id=10&field_az_se_value=1&field_az_clinical_age_value=1
  // $(".az-search-button").click(function(){
  //   var clinic_s_type=$("#az_clinic_select_type").val();
  //   alert(clinic_s_type.val());  console.log();
  // })
  // console.log("fdsfsf")
  // $(".body-page").on('click',function (){
  //   console.log("fdsfsf")
  // })


