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
                id : '',
                name : '',
                sex : '',
                picture : '',
				phone : '',
				email : '',
                birthday : '',
                status : 1,
                user_cards : [],
                user_contacts : []
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