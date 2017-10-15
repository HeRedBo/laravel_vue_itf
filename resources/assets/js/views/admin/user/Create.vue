<template>
        <user-form :userForm="userForm"></user-form>	
</template>

<script>
import UserForm from './Form';
export default {
	components: {UserForm},
    data() {
        return {
           userForm : {
               username : '',
                    id : '',
					password : '',
					name :'',
					phone : '',
					email : '',
					roles : null,
                    picture : '',
                    venues: []
            }
        }
    },

    created() {
       
    },
    
    methods : {
    }
}
</script>