"User strict"

// var context= JSON.parse('{{ ad.notAvailableDays|raw}}');
var day = <? php  json_encode(ad); ?>;
 console.log(day);

// var requete = new XMLHttpRequest();

// requete.onload = function() {
//     //La variable à passer est alors contenue dans l'objet response et l'attribut responseText.
//     var variableARecuperee = this.responseText;
//     console.log(variableARecuperee);
//    };
//    $(document).text(function(){
//     var test = {{ testArray | json_encode|raw }};
// });


// console.log(ad);

// $(document).ready(function(){
//     $("#booking_startDate, #booking_endDate").datepicker({
//         format : "dd/mm/yyyy",
//         datesDisabled : [
//             // {% for day in ad.notAvailableDays %}
//             //     "{{ day.format('d/m/Y') }}",
//             // {% endfor %}
//             ad.notAvailableDays.forEach(function(d){
//                 d.format('YYYY-MM-DD')
//             })
//         ],
//         startDate : new Date()
//     });

//     $('#booking_startDate, #booking_endDate').on('change',calculateAmount);
// })


// function calculateAmount() {
//     let endDate   = new Date($('#booking_endDate').val().replace(/(\d+)\/(\d+)\/(\d{4})/,'$3-$2-$1'));
    
//     let startDate = new Date($('#booking_startDate').val().replace(/(\d+)\/(\d+)\/(\d{4})/,'$3-$2-$1'));

//     if(startDate && endDate && startDate < endDate){
//         let daysTime = 24*60*60*1000;
//         let interval = endDate.getTime() - startDate.getTime();
//         let days     = interval / daysTime;

//         let amount   = days *  ad.price;

//         $('#dasy').text(days);
//         $('#amount').text(amount.toLocaleString('fr-FR'));
//     }
// }