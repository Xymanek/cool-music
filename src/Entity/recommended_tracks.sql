SELECT tracks.* # Normal select for tracks so that we can map to array of entities
FROM tracks
 # get tracks' albums - this is needed to get to artist later
         JOIN albums a2 on tracks.album_id = a2.id

# Find which genres the user rates higher
         JOIN
     (
         SELECT SUM(ratings_with_genre.rating) as total,
                AVG(ratings_with_genre.rating) as mean,
                genre_id
         FROM ( # Get ratings by genre
                  SELECT r.rating, genres.id as genre_id
                  FROM genres
                           JOIN tracks t on genres.id = t.genre_id
                           JOIN reviews r on t.id = r.track_id
                  WHERE r.user_name = ? # Made by current user
              ) as ratings_with_genre
         GROUP BY genre_id # Aggregate by genre
         HAVING mean > 3.5 # Do not consider genres which the user doesn't like (3.5 or lower stars on average)
     ) AS genre_ratings ON genre_ratings.genre_id = tracks.genre_id

# Find which artists the user rates higher
         JOIN
     (
         SELECT SUM(ratings_with_artist.rating) as total,
                AVG(ratings_with_artist.rating) as mean,
                artist_id
         FROM (# Get ratings by artist
                  SELECT r.rating, artists.id as artist_id
                  FROM artists
                           JOIN albums a on artists.id = a.artist_id
                           JOIN tracks t on a.id = t.album_id
                           JOIN reviews r on t.id = r.track_id
                  WHERE r.user_name = ? # Made by current user
              ) as ratings_with_artist
         GROUP BY artist_id # Aggregate by artist
         HAVING mean > 3.5 # Do not consider artists which the user doesn't like (3.5 or lower stars on average)
     ) as artist_ratings ON a2.artist_id = artist_ratings.artist_id

# Exclude reviewed tracks (recommend only the ones user didn't review yet)
WHERE tracks.id NOT IN (SELECT DISTINCT track_id FROM reviews WHERE user_name = ?)

ORDER BY
         (genre_ratings.total + artist_ratings.total) DESC, # First output tracks which have highest artist and genre score (together)
         tracks.id ASC # When multiple tracks have same score, order them by insertion history to prevent different ordering each time

# Do not overwhelm the user with recommendations
LIMIT 15