<template>
	<venue-form :venueForm="venueForm" :select="select"
        :image = "image"
    ></venue-form>	
</template>

<script>
import VenueForm from './Form';
import {stack_error} from 'config/helper';
export default {
	components: { VenueForm},

    data() {
        return {
            venueForm : {},
            select: {},
            image : '' 
        }
    },

    created() {
        this.loadVenue();
    },
    
    methods : {

        loadVenue() {
            //var url = 'venue/edit', that = this, id = this.$route.params.id;
            var that = this, id = this.$route.params.id,  url = 'venue/'+id+'/edit';
            this.$http({
             method :"GET",
             url : url,
          })
          .then(function(response) {
            var responseJson = response.data;
            var data = responseJson.data;
            that.venueForm = data;
            that.value = data.parent_id;
            let {province,city, area} = data;
            let select = {};
            select.province = province;
            select.city = city;
            select.area = area;
            that.select = select;
            that.image  = data.logo;
          })
          .catch(function(error) {
            console.log('load  venue data error')
            stack_error(error);
          }); 
        }
    }
}
</script>