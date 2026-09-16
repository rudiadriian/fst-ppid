import { Editor } from '@tiptap/react';

export const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

/**
 * Checks if a mark exists in the editor schema
 *
 * @param markName - The name of the mark to check
 * @param editor - The editor instance
 */
export const isMarkInSchema = (markName: string, editor: Editor | null) =>
	editor?.schema.spec.marks.get(markName) !== undefined;

/**
 * Checks if a node exists in the editor schema
 *
 * @param nodeName - The name of the node to check
 * @param editor - The editor instance
 */
export const isNodeInSchema = (nodeName: string, editor: Editor | null) =>
	editor?.schema.spec.nodes.get(nodeName) !== undefined;

/**
 * Pengunggah gambar bawaan: berkasnya ditanam sebagai data URL.
 *
 * Dipakai penyunting yang tidak diberi pengunggah sendiri (mis. contoh di
 * halaman dokumentasi). Sebelumnya fungsi ini memalsukan kemajuan unggahan lalu
 * mengembalikan `/images/placeholder-image.png` — berkas yang tidak pernah ada
 * di `public/`, jadi gambar yang disisipkan selalu tampil rusak dan gambar
 * aslinya hilang tanpa pesan apa pun.
 *
 * Modul CMS tidak memakai ini: data URL membengkakkan kolom HTML-nya, jadi
 * di sana `SimpleEditor` diberi `uploadImage` yang menyimpan berkasnya lewat
 * API dan mengembalikan URL biasa.
 */
export const handleImageUpload = async (
	file: File,
	onProgress?: (event: { progress: number }) => void,
	abortSignal?: AbortSignal
): Promise<string> => {
	onProgress?.({ progress: 0 });

	const dataUrl = await convertFileToBase64(file, abortSignal);

	onProgress?.({ progress: 100 });

	return dataUrl;
};

/**
 * Converts a File to base64 string
 */
export const convertFileToBase64 = (file: File, abortSignal?: AbortSignal): Promise<string> => {
	return new Promise((resolve, reject) => {
		const reader = new FileReader();

		const abortHandler = () => {
			reader.abort();
			reject(new Error('Upload cancelled'));
		};

		if (abortSignal) {
			abortSignal.addEventListener('abort', abortHandler);
		}

		reader.onloadend = () => {
			if (abortSignal) {
				abortSignal.removeEventListener('abort', abortHandler);
			}

			if (typeof reader.result === 'string') {
				resolve(reader.result);
			} else {
				reject(new Error('Failed to convert File to base64'));
			}
		};

		reader.onerror = reject;
		reader.readAsDataURL(file);
	});
};
