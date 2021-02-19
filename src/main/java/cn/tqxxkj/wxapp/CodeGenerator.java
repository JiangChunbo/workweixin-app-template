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
	 * FreeMarker 配置类
	 */
	public Configuration configuration;

	/**
	 * 配置属性，包含 key-value
	 */
	public Map<Object, Object> configProperties;

	/**
	 * 模板文件夹 File
	 */
	public File templateDirectory;

	/**
	 * 待检查的 model，key: 文件名，value: 配置文件的 key
	 */
	public Map<String, String> model4Check = new HashMap<String, String>();

	public CodeGenerator(Properties properties) throws Exception {
		
		checkRelation(properties);
		
		
		// 创建配置类
		configuration = new Configuration(Configuration.getVersion());

		// 设置模板所在的目录
		templateDirectory = new File(this.getClass().getResource("/template").toURI());
		configuration.setDirectoryForTemplateLoading(templateDirectory);

		// 设置字符集
		configuration.setDefaultEncoding("utf-8");

		// 将 properties 转为 freemarker 可以识别的 map
		configProperties = props2Map(properties);

		model4Check.put("Branch.php", "branch_table_name");
		model4Check.put("Teacher.php", "teacher_table_name");
		model4Check.put("Student.php", "student_table_name");
		model4Check.put("Class.php", "class_table_name");
		model4Check.put("Parent.php", "parent_table_name");

	}

	/**
	 * 检查实体关联
	 * @param properties
	 */
	private void checkRelation(Properties properties) {
		
	}

	/**
	 * 对外暴露的方法
	 * 
	 * @param appDirectory
	 * @throws Exception
	 */
	public void process(File appDirectory) throws Exception {
		process(templateDirectory, appDirectory);
	}

	/**
	 * 内部处理的方法
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
				System.out.println("未知文件类型");
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
