<template>
        <student-form :studentForm="studentForm"></student-form>	
</template>

<script>
import StudentForm from './Form';
export default {
	components: {StudentForm},
    data() {
        return {
            studentForm : {
               username : '',
                    id : '',
					password : '',
					name :'',
					phone : '',
					email : '',
					roles : null,
                    picture : '',
                    venues: []
            },
            select: {}
        }
    },

    created() {
       
    },
    
    methods : {
    }
}
</script>