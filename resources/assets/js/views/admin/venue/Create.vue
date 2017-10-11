<template>
	<venue-form :venueForm="venueForm" :value ="value" :select="select"></venue-form>	
</template>

<script>
import VenueForm from './Form';
export default {
	components: {VenueForm},
    data() {
        return {
           venueForm : {
               parent_id: ''
            },
            value:'',
            select: {}
        }
    },

    created() {
       
    },
    
    methods : {
    }
}
</script>