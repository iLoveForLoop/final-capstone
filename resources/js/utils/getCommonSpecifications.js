// src/utils/getCommonSpecifications.js

export function getCommonSpecifications(category) {
    switch (category) {
        case 'Catering':
            return [
                'Buffet Setup',
                'Plated Service',
                'Cocktail Service',
                'Tableware Included',
                'Menu Customization',
                'Dessert Station',
                'Wait Staff Included',
                'On-site Chef',
                'Beverage Service',
                'Food Tasting Session',
                'Vegetarian Options',
                'Seafood Menu',
                'Halal Certified',
                'Delivery and Setup',
                'Clean-up After Event',
            ];

        case 'Photography':
            return [
                'Unlimited Shots',
                'Edited Photos Included',
                'Online Gallery Access',
                'USB Copy of Photos',
                'On-site Printing',
                'Drone Photography',
                'Pre-Event Shoot',
                'Studio Session',
                'Event Coverage',
                'Same-Day Edit',
                'Lighting Setup',
                'High-Resolution Output',
            ];

        case 'Videography':
            return [
                'Full Event Coverage',
                'Highlight Reel',
                'Drone Footage',
                '4K Video Quality',
                'Multi-Camera Setup',
                'Edited Video Included',
                'Teaser Trailer',
                'Same-Day Edit',
                'Cinematic Color Grading',
                'Wireless Audio Capture',
                'Storyboarding Session',
            ];

        case 'Lights and Effects':
            return [
                'LED Uplighting',
                'Moving Head Lights',
                'Laser Effects',
                'Fog Machine',
                'Haze Machine',
                'Strobe Lights',
                'Black Lights',
                'Pin Spotting',
                'Gobo Projection',
                'Color Wash',
                'DMX Controlled',
                'Wireless Control',
                'Outdoor Rated',
                'Dance Floor Lighting',
                'Stage Lighting',
                'Mood Lighting',
                'Intelligent Lighting',
                'Follow Spot',
            ];

        case 'Sound System':
            return [
                'Main Speakers',
                'Subwoofers',
                'Stage Monitors',
                'Wireless Microphones',
                'Mixer Console',
                'Sound Technician',
                'Backup Equipment',
                'Power Amplifiers',
                'DJ Setup',
                'Equalization Support',
            ];

        default:
            return [
                'Customizable Service',
                'On-site Support',
                'Delivery and Setup',
                'Professional Staff',
                '24/7 Support Availability',
            ];
    }
}
