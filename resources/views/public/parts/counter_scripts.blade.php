<script>
    
    function count_event(company_id, variable){
        console.log('count_event(' + company_id + ', ' + variable + ')');
        $.post('<?=\URL::action('API\CountController@postCount')?>', {
            company_id: company_id,
            variable: variable,
        }, function(data){
            console.log('count_event data:');
            console.log(data);
        })
    }

    $(document).ready(function(){

        $('a.counter').click(function(){
            
            var company_id = parseInt($(this).attr('company_id'));
            var variable = $(this).attr('variable');
            
            if(!isNaN(company_id) && company_id > 0 && variable != ''){
                count_event(company_id, variable);
            }

        });

    });


</script>