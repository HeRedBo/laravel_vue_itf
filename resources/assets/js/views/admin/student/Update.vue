<template>
        <student-form :studentForm="studentForm"></student-form>	
</template>

<script>
import StudentForm from './Form';
import {stack_error} from 'config/helper';
export default {
	components: {StudentForm},
    data() {
        return {
            studentForm : {
                id : '',
				name :'',
				phone : '',
				email : '',
                picture : '',
                birthday : '',
            },
            select: {}
        }
    },

    created() {
       this.loadStudent();
    },
    
    methods : {
    	loadStudent () {
    		var that = this, id = this.$route.params.id,  url = 'student/'+id+'/edit';
            this.$http({
	             method :"GET",
	             url : url
            })
            .then(function(response) {
                var responseJson = response.data;
                var data = responseJson.data;
                data.birthday = new Date( data.birthday);
                data.sign_up_at = new Date( data.sign_up_at);
                that.studentForm = data;
            })
            .catch(function(error) {
                console.log('load  user data error')
                stack_error(error);
            }); 
    	},
    }
}
</script>