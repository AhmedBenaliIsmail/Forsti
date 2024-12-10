import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import pymysql

connection = pymysql.connect(
    host='127.0.0.1',
    user='root',
    password='',
    database='forsti3 (2)'
)

df_jobs_query = "SELECT job_posting_id AS job_id, title, description, location FROM job_posting;"
df_jobs = pd.read_sql(df_jobs_query, connection)

df_employee_query = "SELECT skills FROM employee WHERE user_id = %s;"
user_id = 1  
with connection.cursor() as cursor:
    cursor.execute(df_employee_query, (user_id,))
    user_skills = cursor.fetchone()[0]  

df_jobs['combined_features'] = df_jobs['title'] + ' ' + df_jobs['description'] + ' ' + df_jobs['location']

vectorizer = TfidfVectorizer()
job_vectors = vectorizer.fit_transform(df_jobs['combined_features'])

user_vector = vectorizer.transform([user_skills])

similarity_scores = cosine_similarity(user_vector, job_vectors)

df_jobs['similarity'] = similarity_scores[0]
top_jobs = df_jobs.sort_values(by='similarity', ascending=False).head(5)

print(top_jobs[['job_id', 'title', 'similarity']])

for _, row in top_jobs.iterrows():
    with connection.cursor() as cursor:
        insert_query = """
        INSERT INTO recommendations (user_id, job_id, similarity_score)
        VALUES (%s, %s, %s)
        """
        cursor.execute(insert_query, (user_id, row['job_id'], row['similarity']))
    connection.commit()

connection.close()