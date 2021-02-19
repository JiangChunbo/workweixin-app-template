package cn.tqxxkj.wxapp;

import java.io.File;
import java.io.FileWriter;
import java.io.Writer;
import java.util.HashMap;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Properties;
import java.util.Set;

import freemarker.template.Configuration;
import freemarker.template.Template;

public class CodeGenerator {

	/**
	 * FreeMarker ������
	 */
	public Configuration configuration;

	/**
	 * �������ԣ����� key-value
	 */
	public Map<Object, Object> configProperties;

	/**
	 * ģ���ļ��� File
	 */
	public File templateDirectory;

	/**
	 * ������ model��key: �ļ�����value: �����ļ��� key
	 */
	public Map<String, String> model4Check = new HashMap<String, String>();

	public CodeGenerator(Properties properties) throws Exception {
		
		checkRelation(properties);
		
		
		// ����������
		configuration = new Configuration(Configuration.getVersion());

		// ����ģ�����ڵ�Ŀ¼
		templateDirectory = new File(this.getClass().getResource("/template").toURI());
		configuration.setDirectoryForTemplateLoading(templateDirectory);

		// �����ַ���
		configuration.setDefaultEncoding("utf-8");

		// �� properties תΪ freemarker ����ʶ��� map
		configProperties = props2Map(properties);

		model4Check.put("Branch.php", "branch_table_name");
		model4Check.put("Teacher.php", "teacher_table_name");
		model4Check.put("Student.php", "student_table_name");
		model4Check.put("Class.php", "class_table_name");
		model4Check.put("Parent.php", "parent_table_name");

	}

	/**
	 * ���ʵ�����
	 * @param properties
	 */
	private void checkRelation(Properties properties) {
		
	}

	/**
	 * ���Ⱪ¶�ķ���
	 * 
	 * @param appDirectory
	 * @throws Exception
	 */
	public void process(File appDirectory) throws Exception {
		process(templateDirectory, appDirectory);
	}

	/**
	 * �ڲ�����ķ���
	 * 
	 * @param temp
	 * @param appDirectory
	 * @throws Exception
	 */
	private void process(File temp, File appDirectory) throws Exception {
		int prefixLength = templateDirectory.getAbsolutePath().length();
		File[] files = temp.listFiles();
		for (File file : files) {
			if (file.isDirectory()) {
				process(file, appDirectory);
			} else if (file.isFile()) {
				if(!checkBeforeCreateFile(file)) {
					continue;
				}
				file.createNewFile();
				System.out.println(file.getPath().substring(prefixLength));
				Template template = configuration.getTemplate(file.getPath().substring(prefixLength));
				File appFile = new File(appDirectory, file.getPath().substring(prefixLength));
				if (!appFile.getParentFile().exists()) {
					appFile.getParentFile().mkdirs();
				}
				appFile.createNewFile();
				Writer out = new FileWriter(appFile);

				template.process(configProperties, out);

				out.close();
			} else {
				System.out.println("δ֪�ļ�����");
			}
		}
	}

	private HashMap<Object, Object> props2Map(Properties properties) {
		Set<Entry<Object, Object>> entrySet = properties.entrySet();
		HashMap<Object, Object> map = new HashMap<Object, Object>();
		for (Entry<Object, Object> entry : entrySet) {
			map.put(entry.getKey(), entry.getValue());
		}
		return map;
	}

	private boolean checkBeforeCreateFile(File file) {
		String filename = file.getName();
		if (model4Check.containsKey(filename) && !configProperties.containsKey(model4Check.get(filename))) {
			return false;
		}
		return true;
	}
}
