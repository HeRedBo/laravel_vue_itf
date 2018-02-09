<template>
        <venue-schedule-form :venueCourseForm="venueCourseForm"></venue-schedule-form>    
</template>
<script>
import VenueScheduleForm from './Form';

export default {
    components: { VenueScheduleForm },
    data() {
        return {
            venueCourseForm : {},
        }
    }
}
</script>