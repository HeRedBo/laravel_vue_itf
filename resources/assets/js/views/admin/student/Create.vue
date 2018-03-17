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
                user_contacts : [],
                auto_create_number : 1
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