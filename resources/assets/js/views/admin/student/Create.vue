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
                name : '',
                id : '',
				password : '',
				name :'',
				phone : '',
				email : '',
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