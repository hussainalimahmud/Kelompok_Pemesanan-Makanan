from flask import Flask, request, jsonify
import joblib
import pandas as pd
import numpy as np

app = Flask(__name__)

# Load Artifacts
model = joblib.load('model_depresi_mahasiswa.pkl')
scaler = joblib.load('scaler_mahasiswa.pkl')
encoders = joblib.load('encoders_mahasiswa.pkl')
feature_columns = joblib.load('feature_columns.pkl')

@app.after_request
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type,Authorization'
    response.headers['Access-Control-Allow-Methods'] = 'GET,POST,OPTIONS'
    return response

@app.route('/predict', methods=['POST', 'OPTIONS'])
def predict():
    if request.method == 'OPTIONS':
        return jsonify({'status': 'ok'}), 200

    try:
        data = request.get_json(force=True)
        input_df = pd.DataFrame([data])[feature_columns]

        # 1. Transform Encoder
        for col, le in encoders.items():
            if col in input_df.columns:
                val_str = str(input_df[col].iloc[0])
                if val_str in le.classes_:
                    input_df[col] = le.transform([val_str])
                else:
                    input_df[col] = 0

        # 2. Scaling & Predict
        scaled_features = scaler.transform(input_df)
        prediction = int(model.predict(scaled_features)[0])
        probability_high = float(model.predict_proba(scaled_features)[0][1])
        probability_low = float(model.predict_proba(scaled_features)[0][0])

        # 3. Hitung Feature Importance (Faktor Utama Pemicu Risiko)
        importances = model.feature_importances_
        feature_importance_dict = dict(zip(feature_columns, [round(val * 100, 2) for val in importances]))
        
        # Urutkan dari yang paling berpengaruh
        sorted_factors = sorted(feature_importance_dict.items(), key=lambda x: x[1], reverse=True)[:3]
        top_factors = [f"{k.replace('_', ' ').title()} ({v}%)" for k, v in sorted_factors]

        # 4. Kategori & Pesan Refleksi
        if prediction == 1:
            kategori = "Tingkat Stress/Risiko Tinggi"
            pesan = "Hasil indikasi menunjukkan kamu sedang mengalami beban pikiran atau tekanan psikologis yang cukup berat. Disarankan untuk beristirahat sejenak dan berkonsultasi dengan Dosen PA atau konselor kampus."
        else:
            kategori = "Tingkat Stress/Risiko Rendah"
            pesan = "Kondisi psikologis dan pola hidupmu relatif stabil dan sehat. Pertahankan manajemen waktu, pola istirahat, dan lingkungan belajar yang positif!"

        # 5. Output JSON (Termasuk Data Chart untuk Frontend)
        response = {
            'status': 'success',
            'hasil_prediksi': prediction,
            'kategori': kategori,
            'catatan_refleksi': pesan,
            'chart_data': {
                'labels': ['Risiko Rendah', 'Risiko Tinggi'],
                'percentages': [round(probability_low * 100, 2), round(probability_high * 100, 2)]
            },
            'faktor_utama': top_factors
        }

        return jsonify(response), 200

    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 400

@app.route('/', methods=['GET'])
def home():
    return jsonify({
        'status': 'online',
        'message': 'API Machine Learning Kesehatan Mental Aktif!',
        'endpoint_predict': '/predict (POST)'
    }), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
