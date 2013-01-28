<?php
/**
 * ImplodeColumnBehavior.php
 * @author kohei hieda
 *
 */
class ImplodeColumnBehavior extends ModelBehavior {

	/**
	 * setup
	 * @param $model
	 * @param $config
	 */
	function setup(&$model, $config) {
		$default = array(
			'delimiter'=>',',
			'fields'=>array());

		$this->settings[$model->alias] = Set::merge($default, $config);
	}

	/**
	 * afterFind
	 * @param $model
	 * @param $queryData
	 * @param $primary
	 * @return array
	 */
	function afterFind(&$model, $results, $primary) {
		if (empty($this->settings[$model->alias]['fields']) || empty($results) || !is_array($results)) {
			return $results;
		}

		foreach (array_keys($results) as $key) {
			if (empty($results[$key][$model->alias])) {
				continue;
			}
			foreach ($this->settings[$model->alias]['fields'] as $field) {
				if (empty($results[$key][$model->alias][$field])) {
					$results[$key][$model->alias][$field] = array();
				} else {
					$results[$key][$model->alias][$field] = explode($this->settings[$model->alias]['delimiter'], $results[$key][$model->alias][$field]);
				}
			}
		}

		return $results;
	}

	/**
	 * beforeSave
	 * @param $model
	 * @return boolean
	 */
	function beforeSave(&$model) {
		if (empty($this->settings[$model->alias]['fields']) || empty($model->data[$model->alias])) {
			return true;
		}

		foreach ($this->settings[$model->alias]['fields'] as $field) {
			if (!isset($model->data[$model->alias][$field])) {
			} else if (empty($model->data[$model->alias][$field]) ||
				!is_array($model->data[$model->alias][$field])) {
				$model->data[$model->alias][$field] = '';
			} else {
				$model->data[$model->alias][$field] = implode($this->settings[$model->alias]['delimiter'], array_values($model->data[$model->alias][$field]));
			}
		}

		return true;
	}

}