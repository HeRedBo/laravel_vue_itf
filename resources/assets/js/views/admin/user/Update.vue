<template>
        <user-form  
            :userForm="userForm"
            :image = "image"
        ></user-form>	
</template>

<script>
import UserForm from './Form';
import {stack_error} from 'config/helper';
export default {
	components: {UserForm},
    data() {
        return {
           userForm : {
               username : '',
					password : '',
					name :'',
					phone : '',
					email : '',
					roles : null,
                    picture : '',
                    venues: []
            },
            image : ''
        }
    },
    
    created() {
        this.loadUser();
    },
    methods : {
        loadUser() {
            var url = 'user/edit', that = this, id = this.$route.params.id;
            this.$http({
             method :"GET",
             url : url,
             params : {
               id : id
             }
            })
            .then(function(response) {
                var responseJson = response.data;
                var data = responseJson.data;
                that.userForm = data;
                that.image  = data.picture;
            })
            .catch(function(error) {
                console.log('load  user data error')
                stack_error(error);
            }); 
        }
    }
}
</script>