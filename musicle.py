import spotipy
import sys
from spotipy.oauth2 import SpotifyClientCredentials
    #if imports are being stupid run python -m pip install spotipy

client_id = "204cc585f9b84f4e82a7a584cd46e836"
client_secret = "210c676b28494d43b231b73c8ddb0c21"

spotify = spotipy.Spotify(client_credentials_manager=SpotifyClientCredentials(client_id=client_id, client_secret=client_secret))

file = open("C:/vscode/MAMP/htdocs/musicle/playlist_id.txt", "r")
playlist_id = file.read()
file.close()
#print(playlist_id)


song_count_json = spotify.playlist_items(playlist_id, fields='total', limit=100, offset=0, market=None, additional_types=('track', 'episode'))
song_count_json = f"{song_count_json}{''}"
song_count_json = song_count_json.split(': ')
song_count_json = song_count_json[1].split('}')
song_count = int(song_count_json[0])
iterations = song_count // 100 + 1
    # iterations is an int containing number of times i need to iterate 100 times since spotify api max is 100

playlist_name_json = spotify.playlist(playlist_id, fields='name')
file = open("C:/vscode/MAMP/htdocs/musicle/playlist_name.txt", "w")

temp = ""
playlist_name = playlist_name_json['name']

j = 0
for char in playlist_name:
    if char.isascii():
        temp = f"{temp}{char}"
        j += 1
playlist_name = temp

file.write(playlist_name)
file.close()


playlist_song_titles = []
playlist_song_artists = []
playlist_song_previews = []

for iterations in range(iterations):
    playlist_items_json = spotify.playlist_items(playlist_id, fields='items(track(name))', limit=100, offset=iterations * 100, market=None, additional_types=('track', 'episode'))
        # returns json info on 100 songs, getting next 100 every iteration if there are more than 100
    playlist_items_json = f"{playlist_items_json}{''}"
        # turns json info into a really long string
    playlist_items_json = playlist_items_json.split('{\'track\': {\'name\': ')
    
    idx = 0
    for song in playlist_items_json:
        playlist_items_json[idx] = song.split('}}')[0]
        if song.startswith("{"):
            playlist_items_json.pop(idx)
        idx += 1
        # split functions to separate track names into their own index in a list
    # playlist_items_json.pop(0)
        # b/c of how split works the first item is garbage info so gets rid of it
    playlist_song_titles.extend(playlist_items_json)
        # adds finalized list of just song titles to the full list of playlist items, then loop iterates if needed
    idx = len(playlist_song_titles)
    for idx in range(idx):
        if playlist_song_titles[idx].startswith("\'"):
            playlist_song_titles[idx] = playlist_song_titles[idx].replace("\'", "")
        if playlist_song_titles[idx].startswith("\""):
            playlist_song_titles[idx] = playlist_song_titles[idx].replace("\"", "")
        playlist_song_titles[idx] = playlist_song_titles[idx].replace("}},", "")
    # format song titles





iterations = song_count // 100 + 1
for iterations in range(iterations):
    playlist_items_json = spotify.playlist_items(playlist_id, fields='items(track(artists(name)))', limit=100, offset=iterations * 100, market=None, additional_types=('track', 'episode'))
    playlist_items_json = f"{playlist_items_json}{''}"
    playlist_items_json = playlist_items_json.split('{\'track\': {\'artists\': [')
    playlist_items_json.pop(0)
    idx = 0
    for song in playlist_items_json:
        playlist_items_json[idx] = song.split('}, {')
        idx += 1

    artist_list = ""
    for list in playlist_items_json:
        for song in list:
            artist_list += song
        playlist_song_artists.append(artist_list)
        artist_list = ""

    idx = len(playlist_song_artists)
    for idx in range(idx):
        playlist_song_artists[idx] = playlist_song_artists[idx].replace("\'name\': ", ", ")
        playlist_song_artists[idx] = playlist_song_artists[idx].replace("}]}", "")
        playlist_song_artists[idx] = playlist_song_artists[idx].replace("{, ", "")
        playlist_song_artists[idx] = playlist_song_artists[idx].replace("}, ", "")
        playlist_song_artists[idx] = playlist_song_artists[idx].replace("\'", "")
# same but for artists

iterations = song_count // 100 + 1
for iterations in range(iterations):
    playlist_song_previews_json = spotify.playlist_items(playlist_id, fields='items(track(external_urls))', limit=100, offset=iterations * 100, market=None, additional_types=('track', 'episode'))
    playlist_song_previews_json = f"{playlist_song_previews_json}{''}"
    playlist_song_previews_json = playlist_song_previews_json.split('{\'track\': {\'external_urls\': {')
    playlist_song_previews_json.pop(0)
    

    playlist_song_previews.extend(playlist_song_previews_json)
    idx = len(playlist_song_previews)
    for idx in range(idx):
        playlist_song_previews[idx] = playlist_song_previews[idx].replace("\'", "")
        playlist_song_previews[idx] = playlist_song_previews[idx].replace("}}}", "")
        playlist_song_previews[idx] = playlist_song_previews[idx].replace("]}", "")
        playlist_song_previews[idx] = playlist_song_previews[idx].replace(",", "")
        playlist_song_previews[idx] = playlist_song_previews[idx].replace("spotify: ", "")
        playlist_song_previews[idx] = playlist_song_previews[idx].replace(" ", "")








# clear the files 
file1 = open("C:/vscode/MAMP/htdocs/musicle/song_names.txt", "w")
file1.write("")
file1.close()
file2 = open("C:/vscode/MAMP/htdocs/musicle/artist_names.txt", "w")
file2.write("")
file2.close()
file3 = open("C:/vscode/MAMP/htdocs/musicle/song_previews.txt", "w")
file3.write("")
file3.close()


# write songs and artists to files, dont write anything that throws an error
song_names_txt = open("C:/vscode/MAMP/htdocs/musicle/song_names.txt", "a")
artist_names_txt = open("C:/vscode/MAMP/htdocs/musicle/artist_names.txt", "a")
song_previews_txt = open("C:/vscode/MAMP/htdocs/musicle/song_previews.txt", "a")
test_txt = open("C:/vscode/MAMP/htdocs/musicle/test.txt", "w")



idx = 0
temp1 = []
temp2 = []
temp3 = []

for idx in range(len(playlist_song_titles)):
    
    try:
        playlist_song_titles[idx].encode("ascii")
        playlist_song_artists[idx].encode("ascii")
        test_txt.write(playlist_song_titles[idx])
        test_txt.write(playlist_song_artists[idx])
        temp1.append(playlist_song_titles[idx])
        temp2.append(playlist_song_artists[idx])
        temp3.append(playlist_song_previews[idx])
    except:
        song_count -= 1

playlist_song_titles = temp1
playlist_song_artists = temp2
playlist_song_previews = temp3

for idx in range(len(playlist_song_titles)):
    if playlist_song_titles[idx].find("�") != -1:
        print(playlist_song_titles[idx].find("�"))
    if playlist_song_artists[idx].find("�") != -1:
        print(f"couldn't import {playlist_song_artists[idx]}")
        
    
    
        

idx = 0
temp_song_titles = []
temp_song_artists = []
temp_song_previews = []
for idx in range(len(playlist_song_titles)):
    if not playlist_song_previews[idx] == "":
        temp_song_titles.append(playlist_song_titles[idx])
        temp_song_artists.append(playlist_song_artists[idx])
        temp_song_previews.append(playlist_song_previews[idx])

playlist_song_titles = temp_song_titles
playlist_song_artists = temp_song_artists
playlist_song_previews = temp_song_previews

song_count = len(playlist_song_titles)


idx = 0
for idx in range(song_count):
    song = playlist_song_titles[idx]
    artist = playlist_song_artists[idx]
    preview = playlist_song_previews[idx]
    song_names_txt.write(song)
    song_names_txt.write("\n")
    artist_names_txt.write(artist)
    artist_names_txt.write("\n")
    song_previews_txt.write(preview)
    song_previews_txt.write("\n")
    idx += 1
    
song_names_txt.close()
artist_names_txt.close()
song_previews_txt.close()

# for preview in playlist_song_previews:
#     print(preview)


    
#print("Songs written to song_names.txt<br>")



# for song in playlist_song_titles:
#     print(song)








# results = f"{results}.{''}"

# results2 = results.split('\'preview_url\': \'')

# preview_urls = []

# for link in results2:
#     if link.startswith('https'):
#         preview_urls.append(link.split('\', \'available_markets\'')[0])

# #print(preview_urls[90])
# count = 0
# for i in preview_urls:
#     count = count + 1

# print(count)









#print(results2[1])
#for track in results['track']:
#    print(track['name'])






#lz_uri = 'spotify:artist:36QJpDe2go2KgaRleHCDTp'


#results = spotify.artist_top_tracks(lz_uri)

#for track in results['tracks'][:10]:
#    print('track    : ' + track['name'])
#    print('audio    : ' + track['preview_url'])
#    print('cover art: ' + track['album']['images'][0]['url'])
#    print()