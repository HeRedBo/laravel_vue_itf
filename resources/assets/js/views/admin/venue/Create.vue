<template>
        <venue-form :venueForm="venueForm" :select="select"></venue-form>	
</template>

<script>
import VenueForm from './Form';
export default {
	components: {VenueForm},
    data() {
        return {
           venueForm : {
               parent_id: 0
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