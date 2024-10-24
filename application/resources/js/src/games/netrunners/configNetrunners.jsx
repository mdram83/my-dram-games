export const configNetrunners = {
    characters: {
        Analyst: {
            classAvatarBorder: ' border-blue-600 ',
            classAvatarText: ' text-blue-400 ',
            imageAvatarS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/analyst-s.jpg'})`,
            imageAvatarM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/analyst-m.jpg'})`,
        },
        Cypher: {
            classAvatarBorder: ' border-green-600 ',
            classAvatarText: ' text-green-400 ',
            imageAvatarS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/cypher-s.jpg'})`,
            imageAvatarM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/cypher-m.jpg'})`,
        },
        Hacker: {
            classAvatarBorder: ' border-pink-600 ',
            classAvatarText: ' text-pink-400 ',
            imageAvatarS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/hacker-s.jpg'})`,
            imageAvatarM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/hacker-m.jpg'})`,
        },
        Hardcore: {
            classAvatarBorder: ' border-red-600 ',
            classAvatarText: ' text-red-400 ',
            imageAvatarS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/hardcore-s.jpg'})`,
            imageAvatarM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/hardcore-m.jpg'})`,
        },
        Speedrunner: {
            classAvatarBorder: ' border-teal-600 ',
            classAvatarText: ' text-teal-400 ',
            imageAvatarS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/speedrunner-s.jpg'})`,
            imageAvatarM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/speedrunner-m.jpg'})`,
        },
        Tank: {
            classAvatarBorder: ' border-orange-600 ',
            classAvatarText: ' text-orange-400 ',
            imageAvatarS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/tank-s.jpg'})`,
            imageAvatarM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/avatars/tank-m.jpg'})`,
        },
    },
    covers: {
        character: {
            imageCoverM: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/covers/random-m.jpg'})`,
            imageCoverS: `url(${window.MyDramGames["asset-url"].img + '/games/netrunners/covers/random-s.jpg'})`,
        },
        location: {
            imageCoverM: 'url(https://images.pexels.com/photos/343457/pexels-photo-343457.jpeg)',
            CrossroadRegular: 'url(https://images.pexels.com/photos/19804230/pexels-photo-19804230/free-photo-of-top-view-of-a-large-intersection.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1)',
        }
    },
    engine: {
        phaseFadeTimeout: 1000,
    }
}
